<?php
/**
 * OrderCreateRequest
 *
 * @copyright Copyright © 2025 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace Mageserv\Yamm\Model\Data;


use Magento\Framework\DataObject;
use Mageserv\Yamm\Api\Data\OrderCreateRequestInterface;

class OrderCreateRequest extends DataObject implements OrderCreateRequestInterface
{

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->_getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerFirstname()
    {
        return $this->_getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerFirstname($customerFirstname)
    {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerLastname()
    {
        return $this->_getData(self::CUSTOMER_LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerLastname($customerLastname)
    {
        return $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress()
    {
        return $this->_getData(self::SHIPPING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress()
    {
        return $this->_getData(self::BILLING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setBillingAddress($billingAddress)
    {
        return $this->setData(self::BILLING_ADDRESS, $billingAddress);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod()
    {
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethod($paymentMethod)
    {
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethod()
    {
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->_getData(self::ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * @inheritDoc
     */
    public function getDiscount()
    {
        return $this->_getData(self::DISCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscount($discount)
    {
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountDescription()
    {
        return $this->_getData(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountDescription($description)
    {
        return $this->setData(self::DISCOUNT_DESCRIPTION, $description);
    }
}