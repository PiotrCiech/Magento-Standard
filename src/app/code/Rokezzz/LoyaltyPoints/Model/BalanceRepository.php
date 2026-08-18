<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Rokezzz\LoyaltyPoints\Api\BalanceRepositoryInterface;
use Rokezzz\LoyaltyPoints\Api\Data\BalanceInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance as BalanceResource;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance\CollectionProvider;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function __construct(
        private readonly BalanceResource $balanceResource,
        private readonly CollectionProvider $balanceCollectionProvider
    ) {
    }

    public function getByCustomerId(int $customerId): BalanceInterface
    {
        $collection = $this->balanceCollectionProvider->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $balance = $collection->getFirstItem();

        if ($balance instanceof Balance && $balance->getId()) {
            return $balance;
        }

        $balance = $collection->getNewEmptyItem();

        if (!$balance instanceof Balance) {
            throw new CouldNotSaveException(__('Invalid loyalty points balance entity.'));
        }

        $balance->setCustomerId($customerId);
        $balance->setPoints(0);

        return $this->save($balance);
    }

    public function save(BalanceInterface $balance): BalanceInterface
    {
        if (!$balance instanceof Balance) {
            throw new CouldNotSaveException(__('Invalid loyalty points balance entity.'));
        }

        $this->balanceResource->save($balance);

        return $balance;
    }
}
