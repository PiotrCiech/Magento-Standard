<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api;

use Rokezzz\LoyaltyPoints\Api\Data\BalanceInterface;

interface BalanceRepositoryInterface
{
    public function getByCustomerId(int $customerId): BalanceInterface;

    public function save(BalanceInterface $balance): BalanceInterface;
}
