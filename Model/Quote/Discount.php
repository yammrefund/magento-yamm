<?php
/**
 * Discount
 *
 * @copyright Copyright © 2025 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace Mageserv\Yamm\Model\Quote;


class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    private const LABEL = "Yamm Discount";
    public const LABEL_DATA_FIELD = "yamm_discount_description";
    public const DISCOUNT_CODE = "yamm_discount";

    public function collect(
        \Magento\Quote\Model\Quote                          $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total            $total
    )
    {
        //Fix for discount applied twice
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        parent::collect($quote, $shippingAssignment, $total);

        if (!$quote->getData(self::DISCOUNT_CODE)) {
            return $this;
        }
        $label = $quote->getData(self::LABEL_DATA_FIELD) ?: self::LABEL;
        $appliedCartDiscount = $total->getDiscountAmount();
        $discountAmount = $total->getDiscountAmount() + $quote->getData(self::DISCOUNT_CODE);
        $total->setDiscountDescription($label);
        $total->setDiscountAmount($discountAmount);
        $total->setBaseDiscountAmount($discountAmount);
        $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

        if (isset($appliedCartDiscount)) {
            $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
        } else {
            $total->addTotalAmount($this->getCode(), $discountAmount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount);
        }

        return $this;
    }
}