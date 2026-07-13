<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferInterface
{
    public const ENTITY_ID = 'entity_id';
    public const REQUEST_ID = 'request_id';
    public const CUSTOMER_ID = 'customer_id';
    public const STATUS = 'status';
    public const INCREMENT_ID = 'increment_id';
    public const ACCESS_TOKEN = 'access_token';
    public const CUSTOMER_NOTE = 'customer_note';
    public const ADMIN_NOTE = 'admin_note';
    public const EXPIRES_AT = 'expires_at';
    public const SENT_AT = 'sent_at';
    public const ACCEPTED_AT = 'accepted_at';
    public const USED_AT = 'used_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_NEW = 'new';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    public function getEntityId(): ?int;

    public function getRequestId(): int;

    public function getCustomerId(): int;

    public function getStatus(): string;

    public function getIncrementId(): string;

    public function getAccessToken(): string;

    public function getCustomerNote(): string;

    public function getAdminNote(): string;

    public function getExpiresAt(): ?string;

    public function getSentAt(): ?string;

    public function getAcceptedAt(): ?string;

    public function getUsedAt(): ?string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function setRequestId(int $requestId): self;

    public function setCustomerId(int $customerId): self;

    public function setStatus(string $status): self;

    public function setIncrementId(string $incrementId): self;

    public function setAccessToken(string $accessToken): self;

    public function setCustomerNote(string $customerNote): self;

    public function setAdminNote(string $adminNote): self;

    public function setExpiresAt(?string $expiresAt): self;

    public function setSentAt(?string $sentAt): self;

    public function setAcceptedAt(?string $acceptedAt): self;

    public function setUsedAt(?string $usedAt): self;
}
