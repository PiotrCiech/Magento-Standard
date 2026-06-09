<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Cron;

use Rokezzz\Omnibus\Model\Config;
use Rokezzz\Omnibus\Model\ResourceModel\PriceHistory;
use Magento\Store\Model\StoreManagerInterface;

class CleanupOldRecords
{
    private const RETENTION_BUFFER_DAYS = 60;

    public function __construct(
        private readonly Config $config,
        private readonly PriceHistory $priceHistory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function execute(): void
    {
        $retentionDays = $this->resolveMaxPeriodDays() + self::RETENTION_BUFFER_DAYS;
        $this->priceHistory->deleteOlderThan($retentionDays);
    }

    private function resolveMaxPeriodDays(): int
    {
        $maxPeriodDays = $this->config->getPeriodDays();

        foreach ($this->storeManager->getStores() as $store) {
            $storePeriodDays = $this->config->getPeriodDays((int) $store->getId());
            if ($storePeriodDays > $maxPeriodDays) {
                $maxPeriodDays = $storePeriodDays;
            }
        }

        return $maxPeriodDays;
    }
}
