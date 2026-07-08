<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_settings/contract_withdrawal/enabled';
    private const XML_PATH_PERIOD_DAYS = 'rokezzz_settings/contract_withdrawal/period_days';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getWithdrawalPeriodDays(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_PERIOD_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return max(1, $value);
    }
}
