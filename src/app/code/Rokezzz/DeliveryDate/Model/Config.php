<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Model;

use DateTimeImmutable;
use DateTimeZone;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_settings/delivery_date/enabled';
    private const XML_PATH_LABEL = 'rokezzz_settings/delivery_date/label';
    private const XML_PATH_MIN_DAYS = 'rokezzz_settings/delivery_date/min_days';
    private const XML_PATH_MAX_DAYS = 'rokezzz_settings/delivery_date/max_days';
    private const XML_PATH_REQUIRED = 'rokezzz_settings/delivery_date/required';
    private const XML_PATH_EXCLUDE_WEEKENDS = 'rokezzz_settings/delivery_date/exclude_weekends';
    private const DEFAULT_MIN_DAYS = 1;
    private const DEFAULT_MAX_DAYS = 30;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly TimezoneInterface $timezone
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

    public function isRequired(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_REQUIRED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isExcludeWeekends(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EXCLUDE_WEEKENDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLabel(?int $storeId = null): string
    {
        $label = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_LABEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        return $label !== '' ? $label : (string) __('Preferred delivery date');
    }

    public function getMinDays(?int $storeId = null): int
    {
        $minDays = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MIN_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $minDays >= 0 ? $minDays : self::DEFAULT_MIN_DAYS;
    }

    public function getMaxDays(?int $storeId = null): int
    {
        $maxDays = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $maxDays > 0 ? $maxDays : self::DEFAULT_MAX_DAYS;
    }

    public function getMinDate(?int $storeId = null): string
    {
        return $this->addDaysToToday($this->getMinDays($storeId), $storeId);
    }

    public function getMaxDate(?int $storeId = null): string
    {
        return $this->addDaysToToday($this->getMaxDays($storeId), $storeId);
    }

    private function addDaysToToday(int $days, ?int $storeId): string
    {
        $timezone = new DateTimeZone($this->timezone->getConfigTimezone(
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
        $date = new DateTimeImmutable('today', $timezone);

        return $date->modify(sprintf('+%d days', $days))->format('Y-m-d');
    }
}
