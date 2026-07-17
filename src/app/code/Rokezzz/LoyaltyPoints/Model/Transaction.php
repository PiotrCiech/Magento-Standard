<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction as TransactionResource;

class Transaction extends AbstractModel implements TransactionInterface
{
    protected function _construct(): void
    {
        $this->_init(TransactionResource::class);
    }

    public function getTransactionId(): ?int
    {
        $transactionId = $this->getData(self::TRANSACTION_ID);

        return !is_null($transactionId) ? (int) $transactionId : null;
    }

    public function setTransactionId(int $transactionId): TransactionInterface
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): TransactionInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    public function setStoreId(int $storeId): TransactionInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function getPoints(): int
    {
        return (int) $this->getData(self::POINTS);
    }

    public function setPoints(int $points): TransactionInterface
    {
        return $this->setData(self::POINTS, $points);
    }

    public function getAction(): string
    {
        return (string) $this->getData(self::ACTION);
    }

    public function setAction(string $action): TransactionInterface
    {
        return $this->setData(self::ACTION, $action);
    }

    public function getOrderId(): ?int
    {
        $orderId = $this->getData(self::ORDER_ID);

        return !is_null($orderId) ? (int) $orderId : null;
    }

    public function setOrderId(?int $orderId): TransactionInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderIncrementId(): ?string
    {
        $orderIncrementId = $this->getData(self::ORDER_INCREMENT_ID);

        return !is_null($orderIncrementId) ? (string) $orderIncrementId : null;
    }

    public function setOrderIncrementId(?string $orderIncrementId): TransactionInterface
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    public function getComment(): ?string
    {
        $comment = $this->getData(self::COMMENT);

        return !is_null($comment) ? (string) $comment : null;
    }

    public function setComment(?string $comment): TransactionInterface
    {
        return $this->setData(self::COMMENT, $comment);
    }

    public function getCreatedAt(): ?string
    {
        $createdAt = $this->getData(self::CREATED_AT);

        return !is_null($createdAt) ? (string) $createdAt : null;
    }

    public function setCreatedAt(string $createdAt): TransactionInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
