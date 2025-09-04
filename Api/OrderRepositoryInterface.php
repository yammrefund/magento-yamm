<?php

namespace Mageserv\Yamm\Api;


interface OrderRepositoryInterface
{
    /**
     * @param \Mageserv\Yamm\Api\Data\FetchRequestInterface $request
     * @return \Mageserv\Yamm\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function fetchOrder($request);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Mageserv\Yamm\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified order.
     *
     * @param string $id The order increment ID.
     * @return \Mageserv\Yamm\Api\Data\OrderInterface interface.
     */
    public function get($id);

    /**
     * Loads a specified order.
     *
     * @param int $id The order ID.
     * @return \Mageserv\Yamm\Api\Data\OrderInterface interface.
     */
    public function getById(int $id);

    /**
     * @param string $orderId
     * @param \Mageserv\Yamm\Api\Data\RefundItemInterface[] $refundItems
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function processRefund($orderId, $refundItems);

    /**
     * @param string $orderId
     * @param \Mageserv\Yamm\Api\Data\RefundItemInterface[] $refundItems
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function prepareRefund($orderId, $refundItems);

    /**
     * @param string $orderId
     * @param \Mageserv\Yamm\Api\Data\RefundItemInterface[] $refundItems
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function rejectRefund($orderId, $refundItems);

    /**
     * @param string $orderId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function cancelRefund($orderId);

    /**
     * Loads a specified order status comment.
     *
     * @param int $id The order status comment ID.
     * @return \Magento\Sales\Api\Data\OrderStatusHistoryInterface Order status history interface.
     */
    public function getOrderHistory($id);

    /**
     * @param int $id
     * @param string $state
     * @param string $status
     * @return \Mageserv\Yamm\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function assignStatus($id, $state, $status);

    /**
     * Get all order statuses with state, status, and status label
     *
     * @return \Mageserv\Yamm\Api\Data\OrderStatusInterface[]
     */
    public function getOrderStatuses();
}
