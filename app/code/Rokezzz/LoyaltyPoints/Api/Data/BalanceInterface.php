<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api\Data;

interface BalanceInterface
{
    public const BALANCE_ID = 'balance_id';
    public const CUSTOMER_ID = 'customer_id';
    public const POINTS = 'points';
    public const UPDATED_AT = 'updated_at';

    public function getBalanceId(): ?int;

    public function setBalanceId(int $balanceId): self;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): self;

    public function getPoints(): int;

    public function setPoints(int $points): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(string $updatedAt): self;
}
