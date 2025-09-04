<?php

namespace Mageserv\Yamm\Api\Data;

interface OrderCreateRequestInterface
{
    const CUSTOMER_ID = "customer_id";

    const SHIPPING_ADDRESS = "shipping_address";
    const BILLING_ADDRESS = "billing_address";
    const PAYMENT_METHOD = "payment_method";
    const SHIPPING_METHOD = "shipping_method";
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
     * @return \Magento\Quote\Api\Data\AddressInterface|null
     */
    public function getShippingAddress();

    /**
     * @param \Magento\Quote\Api\Data\AddressInterface $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * @return \Magento\Quote\Api\Data\AddressInterface|null
     */
    public function getBillingAddress();

    /**
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress);

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
     * @return string|null
     */
    public function getShippingMethod();

    /**
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

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