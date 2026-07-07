<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance as BalanceResource;

class CollectionProvider
{
    public function __construct(
        private readonly EntityFactoryInterface $entityFactory,
        private readonly LoggerInterface $logger,
        private readonly FetchStrategyInterface $fetchStrategy,
        private readonly ManagerInterface $eventManager,
        private readonly BalanceResource $balanceResource
    ) {
    }

    public function create(): Collection
    {
        return new Collection(
            $this->entityFactory,
            $this->logger,
            $this->fetchStrategy,
            $this->eventManager,
            $this->balanceResource->getConnection(),
            $this->balanceResource
        );
    }
}
