<?php

declare(strict_types=1);

namespace Rokezzz\AvailabilityNotification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_STOCK_ALERT_ENABLED = 'catalog/productalert/allow_stock';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STOCK_ALERT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
