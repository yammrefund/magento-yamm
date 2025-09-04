<?php

namespace Mageserv\Yamm\Model;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Mageserv\Yamm\Api\CustomerRepositoryInterface;
use Mageserv\Yamm\Api\Data\OrderInterfaceFactory;
use Mageserv\Yamm\Api\Data\OrderItemInterface;
use Mageserv\Yamm\Api\Data\OrderItemInterfaceFactory;
use Mageserv\Yamm\Api\Data\OrderSearchResultInterface;
use Mageserv\Yamm\Api\Data\OrderSearchResultInterfaceFactory;
use Mageserv\Yamm\Api\Data\OrderStatusInterface;
use Mageserv\Yamm\Api\Data\ResponseInterfaceFactory;
use Mageserv\Yamm\Helper\Data;
use Magento\Sales\Model\RefundOrder;
use Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;
use Mageserv\Yamm\Api\Data\OrderStatusInterfaceFactory;
use Magento\Sales\Model\Order\CreditmemoFactory;

class OrderRepository implements \Mageserv\Yamm\Api\OrderRepositoryInterface
{
    protected $orderFactory;
    protected $responseInterfaceFactory;
    protected $helper;
    protected $orderInterfaceFactory;
    protected $orderItemInterfaceFactory;
    protected $customerRepository;
    protected $productRepository;
    protected $orderRepository;
    protected $orderSearchResultInterfaceFactory;
    protected $itemCreationFactory;
    protected $refundOrder;
    protected $commentCreationInterfaceFactory;
    protected $orderStatusHistoryRepository;
    protected $orderStatusInterfaceFactory;
    protected $orderStatusCollectionFactory;
    protected $creditmemoFactory;
    protected $stockConfiguration;
    protected $creditmemoManagement;
    protected $_itemsToRefund = [];
    protected $_orderItems = [];

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        ResponseInterfaceFactory $responseInterfaceFactory,
        Data $helper,
        OrderInterfaceFactory $orderInterfaceFactory,
        OrderItemInterfaceFactory $orderItemInterfaceFactory,
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository,
        OrderSearchResultInterfaceFactory $orderSearchResultInterfaceFactory,
        ItemCreationFactory $itemCreationFactory,
        RefundOrder $refundOrder,
        \Magento\Sales\Api\Data\CreditmemoCommentCreationInterfaceFactory $commentCreationInterfaceFactory,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        StatusCollectionFactory $orderStatusCollectionFactory,
        OrderStatusInterfaceFactory $orderStatusInterfaceFactory,
        CreditmemoFactory $creditmemoFactory,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        CreditmemoManagementInterface $creditmemoManagement
    ) {
        $this->orderFactory = $orderFactory;
        $this->responseInterfaceFactory = $responseInterfaceFactory;
        $this->helper = $helper;
        $this->orderInterfaceFactory = $orderInterfaceFactory;
        $this->orderItemInterfaceFactory = $orderItemInterfaceFactory;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->orderSearchResultInterfaceFactory = $orderSearchResultInterfaceFactory;
        $this->itemCreationFactory = $itemCreationFactory;
        $this->refundOrder = $refundOrder;
        $this->commentCreationInterfaceFactory = $commentCreationInterfaceFactory;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->orderStatusInterfaceFactory = $orderStatusInterfaceFactory;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->stockConfiguration = $stockConfiguration;
        $this->creditmemoManagement = $creditmemoManagement;
    }

    /**
     * @inheirtdoc
     */
    public function fetchOrder($request)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($request->getIncrementId());
        if (!$request->getMobileNumber() && !$request->getEmail()) {
            throw new NoSuchEntityException(
                __(
                    'Email address or mobile number required. Verify the information and try again!.',
                    $request->getIncrementId()
                )
            );
        }
        if (!$order->getEntityId()) {
            throw new NoSuchEntityException(
                __(
                    'No order with the id "%1" increment id exists. Verify the information and try again!.',
                    $request->getIncrementId()
                )
            );
        }
        if ($request->getEmail() && $request->getEmail() != $order->getCustomerEmail()) {
            throw new InputException(
                __(
                    'Unable to found Order "%1" for email "%2". Verify the information and try again!.',
                    $request->getIncrementId(),
                    $request->getMobileNumber()
                )
            );
        }
        if ($request->getMobileNumber() && ($request->getMobileNumber() != $order->getBillingAddress()->getTelephone() && $request->getMobileNumber() != $order->getShippingAddress()->getTelephone())) {
            throw new InputException(
                __(
                    'Unable to found Order "%1" for mobile number "%2". Verify the information and try again!.',
                    $request->getIncrementId(),
                    $request->getMobileNumber()
                )
            );
        }

        return $this->mapOrdertoYammOrder($order);
    }

    /**
     * @inheridoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $list = $this->orderRepository->getList($searchCriteria);
        $items = $list->getItems();
        $orders = [];
        foreach ($items as $order) {
            $orders[] = $this->mapOrdertoYammOrder($order);
        }
        /** @var OrderSearchResultInterface $results */
        $results = $this->orderSearchResultInterfaceFactory->create();
        $results->setItems($orders)
            ->setSearchCriteria($list->getSearchCriteria())
            ->setTotalCount($list->getTotalCount());
        return $results;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($id);
        return $this->mapOrdertoYammOrder($order);
    }
    /**
     * @inheritdoc
     */
    public function getById(int $id)
    {
        $order = $this->orderFactory->create()->load($id);
        return $this->mapOrdertoYammOrder($order);
    }

    /**
     * @param OrderInterface $order
     * @return \Mageserv\Yamm\Api\Data\OrderInterface
     */
    public function mapOrdertoYammOrder($order)
    {
        /** @var \Mageserv\Yamm\Api\Data\OrderInterface $orderModel */
        $orderModel = $this->orderInterfaceFactory->create(['data' => $order->getData()]);
        $orderModel
            ->setOrderId($order->getId())
            ->setReferenceId($order->getIncrementId())
            ->setAmounts($order->getGrandTotal())
            ->setPaymentMethod($order->getPayment()->getMethod())
            ->setCanReorder($order->canReorder())
            ->setCanCancel($order->canCancel())
            ->setDate($order->getCreatedAt())
            ->setIsPendingPayment($order->getPayment()->getAmountPaid() != $order->getGrandTotal())
            ->setOrderStatus($order->getStatus())
            ->setOrderState($order->getState())
            ->setOrderCurrency($order->getOrderCurrencyCode())
            ->setShowWeight($order->getWeight() > 0)
            ->setTotalWeight($order->getWeight())
            ->setPhoneNumber($order->getBillingAddress()->getTelephone())
            ->setShipping($order->getData('shipping_method'))
            ->setIsPaymentMethodAllowed($this->helper->isAllowedMethod($order->getPayment()->getMethod()))
            ->setFirstCompleteAt($order->getData('first_complete_at'));
        if ($order->getCustomerId()) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            $orderModel->setCustomer($customer);
        }
        $orderItems = [];
        $newItems = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $names = [];
            $pid = $item->getProductId();
            if ($item->getProductType() == "configurable") {
                foreach ($item->getChildrenItems() as $childrenItem) {
                    $names[] = $childrenItem->getName();
                    $pid = $childrenItem->getProductId();
                }
            }
            $name = !empty($names) ? implode('  ', $names) : $item->getName();
            $item->setName($name);

            /** @var OrderItemInterface $orderItem */
            $orderItem = $this->orderItemInterfaceFactory->create();
            $orderItem->setId($pid)
                ->setName($name)
                ->setFinalPrice($item->getPrice())
                ->setTotalDiscount($item->getDiscountAmount())
                ->setPrice($item->getRowTotal())
                ->setQuantity($item->getQtyOrdered())
                ->setSku($item->getSku());
            if (($product = $this->productRepository->get($item->getSku()))) {
                $image = ObjectManager::getInstance()->create(ProductMapper::class)->getProductImage($product);
                $item->setImage($image);
                $mediaGalleryEntries = $product->getMediaGalleryImages();
                $imageUrls = [];
                foreach ($mediaGalleryEntries as $mediaGalleryEntry) {
                    $imageUrls[] = $mediaGalleryEntry->getUrl();
                }
                if (count($imageUrls)) {
                    $orderItem->setImages($imageUrls);
                }
            }
            $orderItems[] = $orderItem;
            $newItems[] = $item;
        }
        $orderModel->setProducts($orderItems)
            ->setItems($newItems);
        return $orderModel;
    }

    /**
     * @inheridoc
     */
    public function processRefund($orderId, $refundItems)
    {

        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        $this->validateForRefund($order, $refundItems);
        $qtys = [];
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $qtys['qtys'][$orderItem->getItemId()] = !empty($this->_itemsToRefund[$orderItem->getSku()]) ? $this->_itemsToRefund[$orderItem->getSku()] : 0;
        }
        $creditmemo = $this->creditmemoFactory->createByOrder($order, $qtys);
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $creditmemoItem->setBackToStock(true);
        }
        if (!$creditmemo->isValidGrandTotal()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The credit memo\'s total must be positive.')
            );
        }
        $creditmemo->addComment(
            __("Your order is refunded by Yamm"),
            true,
            true
        );

        $creditmemo->setCustomerNote(__("Your order is refunded by Yamm"));
        $creditmemo->setCustomerNoteNotify(true);
        $this->creditmemoManagement->refund($creditmemo, true);
        /** @var \Magento\Sales\Api\Data\CreditmemoCommentCreationInterface $comment */
        $state = $order->getTotalRefunded() == $order->getTotalInvoiced() ? \Mageserv\Yamm\Model\Refund::REFUNDED_BY_YAMM : \Mageserv\Yamm\Model\Refund::PARTIAL_REFUND_BY_YAMM;
        $order->setState($state)
            ->setStatus($state);
        $this->orderRepository->save($order);
        return true;
    }

    /**
     * @inheridoc
     */
    public function prepareRefund($orderId, $refundItems)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        $this->validateForRefund($order, $refundItems);
        $order->setLastState($order->getState())
            ->setLastStatus($order->getStatus())
            ->setState(\Mageserv\Yamm\Model\Refund::UNDER_REFUND_BY_YAMM)
            ->setStatus(\Mageserv\Yamm\Model\Refund::UNDER_REFUND_BY_YAMM);
        $this->orderRepository->save($order);
        return true;
    }

    /**
     * @inheridoc
     */
    public function rejectRefund($orderId, $refundItems)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if ($order->getState() !== Refund::UNDER_REFUND_BY_YAMM) {
            throw new LocalizedException(__("Order cannot be rejected because it's not prepared by Yamm"));
        }
        $order->setState(\Mageserv\Yamm\Model\Refund::REFUND_REJECTED_BY_YAMM)
            ->setStatus(\Mageserv\Yamm\Model\Refund::REFUND_REJECTED_BY_YAMM);
        $order->save();
        return true;
    }

    /**
     * @param OrderInterface $order
     * @return void
     * @throws InputException
     */
    private function validateForRefund(OrderInterface $order, $refundItems)
    {
        if (!$order->getPayment()) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Order #%1 cannot be refunded because it doesn't have a valid payment.",
                $order->getIncrementId()));
        }
        if (!$this->helper->isAllowedMethod($order->getPayment()->getMethod())) {
            throw new InputException(__("Order #%1 payment is not applicable by Yamm", $order->getIncrementId()));
        }
        if (!$order->canCreditmemo()) {
            throw new InputException(__("Cannot create a refund for this item"));
        }
        foreach ($refundItems as $refundItem) {
            $this->_itemsToRefund[$refundItem->getSku()] = $refundItem->getQty();
        }
        $errors = [];
        foreach ($order->getAllItems() as $orderItem) {
            if (!in_array($orderItem->getSku(), array_keys($this->_itemsToRefund))) {
                continue;
            }
            if (!$orderItem->canRefund()) {
                $parentItem = $orderItem->getParentItem();
                if (!$parentItem || !$parentItem->canRefund()) {
                    $errors[] = __("Order Item: %1 with SKU: %2 cannot be refunded.", $orderItem->getItemId(),
                        $orderItem->getSku());
                    continue;
                }
            }
            $this->_orderItems[$orderItem->getSku()] = [
                'id' => $orderItem->getParentItem() ? $orderItem->getParentItem()->getItemId() : $orderItem->getItemId(),
                'qty' => $orderItem->getQtyInvoiced()
            ];
        }
        if (count($errors)) {
            throw new InputException(__("Cannot refund order #%1. Reasons: %2", $order->getIncrementId(),
                implode("\n", $errors)));
        }

        $notFounds = array_diff_key($this->_itemsToRefund, $this->_orderItems);
        if (count($notFounds)) {
            throw new InputException(__("Cannot refund order #%1. SKUs %2 not found", $order->getIncrementId(),
                implode("\n", $notFounds)));
        }
        $countErrors = [];
        foreach ($this->_itemsToRefund as $sku => $qty) {
            if ($qty > $this->_orderItems[$sku]['qty']) {
                $countErrors[] = __("SKU %1 refunded quantity cannot be greater than invoice quantity %2", $sku,
                    $this->_orderItems[$sku]['qty']);
            }
        }
        if (count($countErrors)) {
            throw new InputException(__("Cannot refund order #%1. Reasons: %2", $order->getIncrementId(),
                implode("\n", $countErrors)));
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrderHistory($id)
    {
        return $this->orderStatusHistoryRepository->get($id);
    }

    /**
     * @inheritDoc
     */
    public function assignStatus($id, $state, $status)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($id);
        $order->setState($state)
            ->setStatus($status);
        $order->save();
        return $this->mapOrdertoYammOrder($order);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderStatuses()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Status\Collection $statusCollection */
        $statusCollection = $this->orderStatusCollectionFactory->create();
        $statusCollection->joinStates();
        $statuses = [];
        /** @var \Magento\Sales\Model\Order\Status $status */
        foreach ($statusCollection as $status) {
            /** @var OrderStatusInterface $statusObject */
            $statusObject = $this->orderStatusInterfaceFactory->create();
            $statusObject->setId($status->getId());
            $statusObject->setState($status->getState());
            $statusObject->setStatus($status->getStatus());
            $statusObject->setStatusLabel($status->getLabel());
            $statuses[] = $statusObject;
        }
        return $statuses;
    }

    /**
     * @inheritDoc
     */
    public function cancelRefund($orderId)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if ($order->getLastState() && $order->getLastStatus()) {
            $order->setState($order->getLastState())
                ->setStatus($order->getLastStatus())
                ->setLastState(null)
                ->setLastStatus(null);
            $order->save();
        }
        throw new LocalizedException(__("Refund request cannot be canceled!. Cannot find old status"));
    }
}
