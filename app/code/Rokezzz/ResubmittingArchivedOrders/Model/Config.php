<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_resubmitting_archived_orders/general/enabled';
    private const XML_PATH_ARCHIVE_AGE_DAYS = 'rokezzz_resubmitting_archived_orders/archive/age_days';
    private const XML_PATH_ARCHIVE_STATUSES = 'rokezzz_resubmitting_archived_orders/archive/statuses';

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

    public function getArchiveAgeDays(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ARCHIVE_AGE_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string[]
     */
    public function getArchiveStatuses(?int $storeId = null): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_ARCHIVE_STATUSES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
