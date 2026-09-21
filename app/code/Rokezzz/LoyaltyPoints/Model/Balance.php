<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\LoyaltyPoints\Api\Data\BalanceInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance as BalanceResource;

class Balance extends AbstractModel implements BalanceInterface
{
    protected function _construct(): void
    {
        $this->_init(BalanceResource::class);
    }

    public function getBalanceId(): ?int
    {
        $balanceId = $this->getData(self::BALANCE_ID);

        return !is_null($balanceId) ? (int) $balanceId : null;
    }

    public function setBalanceId(int $balanceId): BalanceInterface
    {
        return $this->setData(self::BALANCE_ID, $balanceId);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): BalanceInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getPoints(): int
    {
        return (int) $this->getData(self::POINTS);
    }

    public function setPoints(int $points): BalanceInterface
    {
        return $this->setData(self::POINTS, max(0, $points));
    }

    public function getUpdatedAt(): ?string
    {
        $updatedAt = $this->getData(self::UPDATED_AT);

        return !is_null($updatedAt) ? (string) $updatedAt : null;
    }

    public function setUpdatedAt(string $updatedAt): BalanceInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
