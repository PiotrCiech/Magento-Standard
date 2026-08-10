<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Reorder as ReorderHelper;
use Magento\Sales\Model\Order;

final class ArchivedOrderResubmitValidator
{
    private const ARCHIVED_FLAG = 'rokezzz_is_archived';

    public function __construct(
        private readonly Config $config,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ReorderHelper $reorderHelper
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function getOrderForResubmit(int $customerId, int $orderId): OrderInterface
    {
        $this->assertFeatureEnabled();

        $order = $this->orderRepository->get($orderId);

        $this->assertCustomerOwnsOrder($order, $customerId);
        $this->assertOrderIsArchived($order);
        $this->assertReorderAllowed($order);

        return $order;
    }

    /**
     * @throws LocalizedException
     */
    private function assertFeatureEnabled(): void
    {
        if ($this->config->isEnabled()) {
            return;
        }

        throw new LocalizedException(__('Resubmitting archived orders is disabled.'));
    }

    /**
     * @throws LocalizedException
     */
    private function assertCustomerOwnsOrder(OrderInterface $order, int $customerId): void
    {
        if ((int) $order->getCustomerId() === $customerId) {
            return;
        }

        throw new LocalizedException(__('We cannot resubmit this order.'));
    }

    /**
     * @throws LocalizedException
     */
    private function assertOrderIsArchived(OrderInterface $order): void
    {
        if ($order instanceof Order && (int) $order->getData(self::ARCHIVED_FLAG) === 1) {
            return;
        }

        throw new LocalizedException(__('This order is not archived.'));
    }

    /**
     * @throws LocalizedException
     */
    private function assertReorderAllowed(OrderInterface $order): void
    {
        if ($this->reorderHelper->isAllowed($order->getStoreId())) {
            return;
        }

        throw new LocalizedException(__('Reorder is not allowed in this store.'));
    }
}
