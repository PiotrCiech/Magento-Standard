<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Api\Data;

interface WithdrawalRequestInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ID = 'order_id';
    public const CUSTOMER_ID = 'customer_id';
    public const STATUS = 'status';
    public const STATEMENT_TEXT = 'statement_text';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public function getEntityId(): ?int;

    public function getOrderId(): int;

    public function getCustomerId(): int;

    public function getStatus(): string;

    public function getStatementText(): string;

    public function getCreatedAt(): string;

    public function setOrderId(int $orderId): self;

    public function setCustomerId(int $customerId): self;

    public function setStatus(string $status): self;

    public function setStatementText(string $statementText): self;
}
