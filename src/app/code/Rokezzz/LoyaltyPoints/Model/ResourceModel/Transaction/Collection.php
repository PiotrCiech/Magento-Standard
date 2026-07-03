<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction as TransactionResource;
use Rokezzz\LoyaltyPoints\Model\Transaction;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Transaction::class, TransactionResource::class);
    }
}
