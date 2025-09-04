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
    public function getShippingInformation()
    {
        return $this->_getData(self::SHIPPING_INFORMATION);
    }

    /**
     * @inheritDoc
     */
    public function setShippingInformation($shippingInformation)
    {
        return $this->setData(self::SHIPPING_INFORMATION, $shippingInformation);
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