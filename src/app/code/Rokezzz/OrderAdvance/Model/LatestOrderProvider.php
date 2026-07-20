<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

final class LatestOrderProvider
{
    /**
     * @var list<string>
     */
    private const HIDDEN_STATES = [
        Order::STATE_COMPLETE,
        Order::STATE_CLOSED,
        Order::STATE_CANCELED,
    ];

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder
    ) {
    }

    public function getLatestOrder(int $customerId): ?Order
    {
        if ($customerId === 0) {
            return null;
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField(OrderInterface::CREATED_AT)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderInterface::CUSTOMER_ID, $customerId)
            ->addFilter(OrderInterface::STATE, self::HIDDEN_STATES, 'nin')
            ->addSortOrder($sortOrder)
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = array_shift($orders);

        return $order instanceof Order ? $order : null;
    }
}
