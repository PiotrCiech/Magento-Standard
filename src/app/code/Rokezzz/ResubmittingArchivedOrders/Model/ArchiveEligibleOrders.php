<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Model;

use DateTimeImmutable;
use DateTimeZone;
use Magento\Framework\Stdlib\DateTime;
use Rokezzz\ResubmittingArchivedOrders\Model\ResourceModel\OrderArchive;

class ArchiveEligibleOrders
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderArchive $orderArchive
    ) {
    }

    public function execute(): int
    {
        if (!$this->config->isEnabled()) {
            return 0;
        }

        $ageDays = $this->config->getArchiveAgeDays();
        $statuses = $this->config->getArchiveStatuses();

        if ($ageDays <= 0 || $statuses === []) {
            return 0;
        }

        $threshold = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->modify(sprintf('-%d days', $ageDays))
            ->format(DateTime::DATETIME_PHP_FORMAT);

        return $this->orderArchive->archiveOrdersCreatedBefore($threshold, $statuses);
    }
}
