<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Rokezzz\LoyaltyPoints\Api\TransactionRepositoryInterface;

class TransactionRecorder
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function record(
        int $customerId,
        int $pointsDelta,
        string $action,
        int $storeId,
        ?int $orderId = null,
        ?string $orderIncrementId = null,
        ?string $comment = null
    ): void {
        $transaction = $this->transactionRepository->createNew();
        $transaction
            ->setCustomerId($customerId)
            ->setStoreId($storeId)
            ->setPoints($pointsDelta)
            ->setAction($action)
            ->setOrderId($orderId)
            ->setOrderIncrementId($orderIncrementId)
            ->setComment($comment);

        $this->transactionRepository->save($transaction);
    }
}
