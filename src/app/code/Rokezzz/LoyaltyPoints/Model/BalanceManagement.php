<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\TransactionRepositoryInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance as BalanceResource;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance\CollectionFactory as BalanceCollectionFactory;

class BalanceManagement implements BalanceManagementInterface
{
    public function __construct(
        private readonly BalanceFactory $balanceFactory,
        private readonly BalanceResource $balanceResource,
        private readonly BalanceCollectionFactory $balanceCollectionFactory,
        private readonly TransactionFactory $transactionFactory,
        private readonly TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function getBalance(int $customerId): int
    {
        $balance = $this->loadBalance($customerId);

        return $balance->getPoints();
    }

    public function addPoints(
        int $customerId,
        int $points,
        string $action,
        int $storeId,
        ?int $orderId = null,
        ?string $orderIncrementId = null,
        ?string $comment = null
    ): void {
        if ($points <= 0) {
            return;
        }

        $this->changeBalance(
            $customerId,
            $points,
            $action,
            $storeId,
            $orderId,
            $orderIncrementId,
            $comment
        );
    }

    public function deductPoints(
        int $customerId,
        int $points,
        string $action,
        int $storeId,
        ?int $orderId = null,
        ?string $orderIncrementId = null,
        ?string $comment = null
    ): void {
        if ($points <= 0) {
            return;
        }

        $this->changeBalance(
            $customerId,
            -$points,
            $action,
            $storeId,
            $orderId,
            $orderIncrementId,
            $comment
        );
    }

    private function changeBalance(
        int $customerId,
        int $pointsDelta,
        string $action,
        int $storeId,
        ?int $orderId,
        ?string $orderIncrementId,
        ?string $comment
    ): void {
        $balance = $this->loadBalance($customerId);
        $newBalance = $balance->getPoints() + $pointsDelta;

        if ($newBalance < 0) {
            throw new LocalizedException(__('Insufficient loyalty points balance.'));
        }

        $balance->setPoints($newBalance);
        $this->balanceResource->save($balance);

        $transaction = $this->transactionFactory->create();
        $transaction->setCustomerId($customerId);
        $transaction->setStoreId($storeId);
        $transaction->setPoints($pointsDelta);
        $transaction->setAction($action);
        $transaction->setOrderId($orderId);
        $transaction->setOrderIncrementId($orderIncrementId);
        $transaction->setComment($comment);

        $this->transactionRepository->save($transaction);
    }

    private function loadBalance(int $customerId): Balance
    {
        $collection = $this->balanceCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $balance = $collection->getFirstItem();

        if (!$balance->getId()) {
            $balance = $this->balanceFactory->create();
            $balance->setCustomerId($customerId);
            $balance->setPoints(0);
            $this->balanceResource->save($balance);
        }

        return $balance;
    }
}
