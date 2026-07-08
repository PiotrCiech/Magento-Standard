<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class WithdrawalRequestResource extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('rokezzz_contract_withdrawal_request', 'entity_id');
    }
}
