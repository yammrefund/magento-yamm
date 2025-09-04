<?php

namespace Mageserv\Yamm\Api\Service;

interface CreateOrderInterface
{
    /**
     * @param \Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order
     * @return \Mageserv\Yamm\Api\Data\OrderInterface
     */
    public function execute(\Mageserv\Yamm\Api\Data\OrderCreateRequestInterface $order);
}