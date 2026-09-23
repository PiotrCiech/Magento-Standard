<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model\ResourceModel\WithdrawalRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\ContractWithdrawal\Model\ResourceModel\WithdrawalRequestResource;
use Rokezzz\ContractWithdrawal\Model\WithdrawalRequest as WithdrawalRequestModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(WithdrawalRequestModel::class, WithdrawalRequestResource::class);
    }
}
