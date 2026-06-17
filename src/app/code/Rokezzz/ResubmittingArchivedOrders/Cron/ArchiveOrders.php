<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Cron;

use Rokezzz\ResubmittingArchivedOrders\Model\ArchiveEligibleOrders;

class ArchiveOrders
{
    public function __construct(
        private readonly ArchiveEligibleOrders $archiveEligibleOrders
    ) {
    }

    public function execute(): void
    {
        $this->archiveEligibleOrders->execute();
    }
}
