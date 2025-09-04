<?php

namespace Mageserv\Yamm\Api\Data;

interface OrderCreateRequestInterface
{
    const CUSTOMER_ID = "customer_id";
    const SHIPPING_INFORMATION = "shipping_information";
    const PAYMENT_METHOD = "payment_method";
    const ITEMS = "items";
    const DISCOUNT = "discount";
    const DISCOUNT_DESCRIPTION = "discount_description";
    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);


    /**
     * @return \Magento\Checkout\Api\Data\ShippingInformationInterface|null
     */
    public function getShippingInformation();

    /**
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $shippingInformation
     * @return $this
     */
    public function setShippingInformation($shippingInformation);

    /**
     * @return string|null
     */
    public function getPaymentMethod();

    /**
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @return \Magento\Quote\Api\Data\CartItemInterface[]|null
     */
    public function getItems();

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return float|null
     */
    public function getDiscount();

    /**
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount);

    /**
     * @return string|null
     */
    public function getDiscountDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDiscountDescription($description);
}