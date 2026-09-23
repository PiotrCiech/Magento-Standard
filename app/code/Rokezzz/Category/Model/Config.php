<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLE_NEW = 'rokezzz_category/general/enable_new';
    private const XML_PATH_ENABLE_BESTSELLERS = 'rokezzz_category/general/enable_bestsellers';
    private const XML_PATH_ENABLE_BESTSELLER_BADGE = 'rokezzz_category/product_labels/enable_bestseller_badge';
    private const XML_PATH_ENABLE_DISCOUNT_BADGE = 'rokezzz_category/product_labels/enable_discount_badge';
    private const XML_PATH_ENABLE_LOW_STOCK_BADGE = 'rokezzz_category/product_labels/enable_low_stock_badge';
    private const XML_PATH_LOW_STOCK_THRESHOLD = 'rokezzz_category/product_labels/low_stock_threshold';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isNewEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_NEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isBestsellersEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_BESTSELLERS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isBestsellerBadgeEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_BESTSELLER_BADGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isDiscountBadgeEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_DISCOUNT_BADGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isLowStockBadgeEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_LOW_STOCK_BADGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLowStockThreshold(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_LOW_STOCK_THRESHOLD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value > 0 ? $value : 5;
    }
}
