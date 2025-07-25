<?php

namespace Mageserv\Yamm\Api\Data;

interface OrderSearchResultInterface  extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Mageserv\Yamm\Api\Data\OrderInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Mageserv\Yamm\Api\Data\OrderInterface[]|null $items
     * @return $this
     */
    public function setItems(?array $items = null);
}
