<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Cron;

use Rokezzz\Omnibus\Model\RecordAllProductPrices;

class RecordPrices
{
    public function __construct(
        private readonly RecordAllProductPrices $recordAllProductPrices
    ) {
    }

    public function execute(): void
    {
        $this->recordAllProductPrices->execute();
    }
}
