<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\BalanceRepositoryInterface;
use Throwable;

class BalanceManagement implements BalanceManagementInterface
{
    public function __construct(
        private readonly BalanceRepositoryInterface $balanceRepository,
        private readonly TransactionRecorder $transactionRecorder,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    public function getBalance(int $customerId): int
    {
        return $this->balanceRepository->getByCustomerId($customerId)->getPoints();
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
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        try {
            $balance = $this->balanceRepository->getByCustomerId($customerId);
            $newBalance = $balance->getPoints() + $pointsDelta;

            if ($newBalance < 0) {
                throw new LocalizedException(__('Insufficient loyalty points balance.'));
            }

            $balance->setPoints($newBalance);
            $this->balanceRepository->save($balance);

            $this->transactionRecorder->record(
                $customerId,
                $pointsDelta,
                $action,
                $storeId,
                $orderId,
                $orderIncrementId,
                $comment
            );

            $connection->commit();
        } catch (Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }
    }
}
