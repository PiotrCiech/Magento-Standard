<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferRequestInterface
{
    public const ENTITY_ID = 'entity_id';
    public const CUSTOMER_ID = 'customer_id';
    public const STATUS = 'status';
    public const CUSTOMER_NOTE = 'customer_note';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_NEW = 'new';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    public function getEntityId(): ?int;

    public function getCustomerId(): int;

    public function getStatus(): string;

    public function getCustomerNote(): string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function setCustomerId(int $customerId): self;

    public function setStatus(string $status): self;

    public function setCustomerNote(string $customerNote): self;
}
