<?php
/**
 * CreateOrder
 *
 * @copyright Copyright © 2025 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace Mageserv\Yamm\Model\Service;


use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Mageserv\Yamm\Api\OrderRepositoryInterface;
use Mageserv\Yamm\Api\Service\CreateOrderInterface;

class CreateOrder implements CreateOrderInterface
{
    public function __construct(
        private readonly CartManagementInterface $quoteManagement,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OrderRepositoryInterface $orderRepository
    )
    {
    }


    public function execute(\Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order): \Mageserv\Yamm\Api\Data\OrderInterface
    {
        try{
            $this->validateRequest($order);
            $quote = $this->createQuote($order);
            $quote->setShippingAddress($order->getShippingAddress())
                ->setBillingAddress($order->getBillingAddress());
            $quote->setItems($order->getItems());
            $quote->getShippingAddress()->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod($order->getShippingMethod());
            $quote->getPayment()->setMethod($order->getPaymentMethod());
            if($order->getDiscount()){
                $this->applyCustomDiscount($quote, $order->getDiscount(), $order->getDiscountDescription());
            }
            $quote->collectTotals();
            $this->cartRepository->save($quote);
            $order = $this->quoteManagement->submit($quote);
            return $this->orderRepository->get($order->getId());
        }catch (\Exception $e){
            throw new LocalizedException(__('Unable to create order. %1', $e->getMessage() ));
        }
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function validateRequest(\Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order): void
    {
        if($order->getCustomerId()){
            try{
                $this->customerRepository->getById($order->getCustomerId());
            }catch (NoSuchEntityException $e){
                throw new LocalizedException(__('Customer with id %1 not found', $order->getCustomerId()));
            }
        }
        if(!$order->getCustomerId()){
            if(!$order->getCustomerEmail()){
                throw new LocalizedException(__('Customer Email is required'));
            }
            if(!$order->getCustomerFirstname()){
                throw new LocalizedException(__('Customer First name is required'));
            }
            if(!$order->getCustomerLastname()){
                throw new LocalizedException(__('Customer Last name is required'));
            }
        }
        if(!$order->getPaymentMethod()){
            throw new LocalizedException(__('Payment Method is required'));
        }
        if(!$order->getShippingMethod()){
            throw new LocalizedException(__('Shipping Method is required'));
        }
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    private function createQuote(\Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order): CartInterface
    {
        if($order->getCustomerId()){
            $quoteId =  $this->quoteManagement->createEmptyCartForCustomer($order->getCustomerId());
        }else{
            $quoteId =  $this->quoteManagement->createEmptyCart();
        }
        return $this->cartRepository->get($quoteId);
    }

    private function applyCustomDiscount(CartInterface $quote, ?float $discount = 0, ?string $discountDescription = null)
    {
        if(!$discountDescription){
            $discountDescription = __("Yamm Discount");
        }
        $quote->setData('yamm_discount', -$discount);
        $quote->setData('yamm_discount_description', $discountDescription);
        foreach($quote->getAllAddresses() as $address){
            $address->setDiscountAmount(-$discount)
                ->setBaseDiscountAmount(-$discount)
                ->setDiscountDescription($discountDescription);
        }
    }
}