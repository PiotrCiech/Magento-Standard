<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api;

interface BalanceManagementInterface
{
    public function getBalance(int $customerId): int;

    public function addPoints(
        int $customerId,
        int $points,
        string $action,
        int $storeId,
        ?int $orderId = null,
        ?string $orderIncrementId = null,
        ?string $comment = null
    ): void;

    public function deductPoints(
        int $customerId,
        int $points,
        string $action,
        int $storeId,
        ?int $orderId = null,
        ?string $orderIncrementId = null,
        ?string $comment = null
    ): void;
}
