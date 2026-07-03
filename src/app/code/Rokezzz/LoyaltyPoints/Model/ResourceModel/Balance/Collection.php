<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\LoyaltyPoints\Model\Balance;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Balance as BalanceResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Balance::class, BalanceResource::class);
    }
}
