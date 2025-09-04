<?php

namespace Mageserv\Yamm\Api\Service;

use Magento\Framework\Exception\LocalizedException;

interface CreateOrderInterface
{
    /**
     * @param \Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order
     * @return \Mageserv\Yamm\Api\Data\OrderInterface
     * @throws LocalizedException
     */
    public function execute(\Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order): \Mageserv\Yamm\Api\Data\OrderInterface;
}