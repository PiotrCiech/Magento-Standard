<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_settings/loyalty_points/enabled';
    private const XML_PATH_POINTS_PER_CURRENCY = 'rokezzz_settings/loyalty_points/points_per_currency';
    private const XML_PATH_POINT_VALUE = 'rokezzz_settings/loyalty_points/point_value';
    private const XML_PATH_MIN_POINTS_TO_REDEEM = 'rokezzz_settings/loyalty_points/min_points_to_redeem';
    private const XML_PATH_MAX_REDEMPTION_PERCENT = 'rokezzz_settings/loyalty_points/max_redemption_percent';
    private const DEFAULT_POINTS_PER_CURRENCY = 1.0;
    private const DEFAULT_POINT_VALUE = 0.01;
    private const DEFAULT_MIN_POINTS_TO_REDEEM = 100;
    private const DEFAULT_MAX_REDEMPTION_PERCENT = 50.0;

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

    public function getPointsPerCurrency(?int $storeId = null): float
    {
        $value = (float) $this->scopeConfig->getValue(
            self::XML_PATH_POINTS_PER_CURRENCY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value > 0 ? $value : self::DEFAULT_POINTS_PER_CURRENCY;
    }

    public function getPointValue(?int $storeId = null): float
    {
        $value = (float) $this->scopeConfig->getValue(
            self::XML_PATH_POINT_VALUE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value > 0 ? $value : self::DEFAULT_POINT_VALUE;
    }

    public function getMinPointsToRedeem(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MIN_POINTS_TO_REDEEM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value > 0 ? $value : self::DEFAULT_MIN_POINTS_TO_REDEEM;
    }

    public function getMaxRedemptionPercent(?int $storeId = null): float
    {
        $value = (float) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_REDEMPTION_PERCENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($value <= 0) {
            return self::DEFAULT_MAX_REDEMPTION_PERCENT;
        }

        return min($value, 100.0);
    }
}
