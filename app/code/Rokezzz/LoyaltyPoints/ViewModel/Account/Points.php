<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\ViewModel\Account;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Api\TransactionRepositoryInterface;
use Rokezzz\LoyaltyPoints\Model\Config;

class Points implements ArgumentInterface
{
    private const TRANSACTIONS_PAGE_SIZE = 50;

    public function __construct(
        private readonly Config $config,
        private readonly CustomerSession $customerSession,
        private readonly BalanceManagementInterface $balanceManagement,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    public function getBalance(): int
    {
        if (!$this->customerSession->isLoggedIn()) {
            return 0;
        }

        return $this->balanceManagement->getBalance((int) $this->customerSession->getCustomerId());
    }

    /**
     * @return TransactionInterface[]
     */
    public function getTransactions(): array
    {
        if (!$this->customerSession->isLoggedIn()) {
            return [];
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField(TransactionInterface::CREATED_AT)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::CUSTOMER_ID, (int) $this->customerSession->getCustomerId())
            ->addSortOrder($sortOrder)
            ->setPageSize(self::TRANSACTIONS_PAGE_SIZE)
            ->create();

        return $this->transactionRepository->getList($searchCriteria)->getItems();
    }

    public function getActionLabel(string $action): string
    {
        return match ($action) {
            TransactionInterface::ACTION_EARN => (string) __('Earned'),
            TransactionInterface::ACTION_REDEEM => (string) __('Redeemed'),
            TransactionInterface::ACTION_CANCEL_EARN => (string) __('Earned points reversed'),
            TransactionInterface::ACTION_CANCEL_REDEEM => (string) __('Redeemed points refunded'),
            default => $action,
        };
    }
}
