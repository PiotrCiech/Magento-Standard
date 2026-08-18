<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_omnibus/general/enabled';
    private const XML_PATH_PERIOD_DAYS = 'rokezzz_omnibus/general/period_days';
    private const XML_PATH_SHOW_ON_PDP = 'rokezzz_omnibus/general/show_on_pdp';
    private const XML_PATH_SHOW_ON_LISTING = 'rokezzz_omnibus/general/show_on_listing';

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

    public function getPeriodDays(?int $storeId = null): int
    {
        $days = (int) $this->scopeConfig->getValue(
            self::XML_PATH_PERIOD_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $days > 0 ? $days : 30;
    }

    public function isShowOnPdp(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_ON_PDP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isShowOnListing(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_ON_LISTING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
