<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Balance extends AbstractDb
{
    private const TABLE_NAME = 'rokezzz_loyalty_points_balance';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'balance_id');
    }
}
