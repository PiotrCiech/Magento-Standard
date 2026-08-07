<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_mostpopular/general/enabled';
    private const XML_PATH_PRODUCTS_COUNT = 'rokezzz_mostpopular/general/products_count';
    private const XML_PATH_PERIOD = 'rokezzz_mostpopular/general/period';

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

    public function getProductsCount(?int $storeId = null): int
    {
        $count = (int) $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCTS_COUNT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $count > 0 ? $count : 8;
    }

    public function getPeriod(?int $storeId = null): string
    {
        $period = (string) $this->scopeConfig->getValue(
            self::XML_PATH_PERIOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return in_array($period, ['day', 'month', 'year'], true) ? $period : 'month';
    }
}
