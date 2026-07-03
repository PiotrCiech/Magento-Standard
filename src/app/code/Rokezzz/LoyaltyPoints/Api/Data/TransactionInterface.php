<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api\Data;

interface TransactionInterface
{
    public const TRANSACTION_ID = 'transaction_id';
    public const CUSTOMER_ID = 'customer_id';
    public const STORE_ID = 'store_id';
    public const POINTS = 'points';
    public const ACTION = 'action';
    public const ORDER_ID = 'order_id';
    public const ORDER_INCREMENT_ID = 'order_increment_id';
    public const COMMENT = 'comment';
    public const CREATED_AT = 'created_at';

    public const ACTION_EARN = 'earn';
    public const ACTION_REDEEM = 'redeem';
    public const ACTION_CANCEL_EARN = 'cancel_earn';
    public const ACTION_CANCEL_REDEEM = 'cancel_redeem';

    public function getTransactionId(): ?int;

    public function setTransactionId(int $transactionId): self;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): self;

    public function getStoreId(): int;

    public function setStoreId(int $storeId): self;

    public function getPoints(): int;

    public function setPoints(int $points): self;

    public function getAction(): string;

    public function setAction(string $action): self;

    public function getOrderId(): ?int;

    public function setOrderId(?int $orderId): self;

    public function getOrderIncrementId(): ?string;

    public function setOrderIncrementId(?string $orderIncrementId): self;

    public function getComment(): ?string;

    public function setComment(?string $comment): self;

    public function getCreatedAt(): ?string;

    public function setCreatedAt(string $createdAt): self;
}
