<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model\Data;

use Rokezzz\Omnibus\Api\Data\OmnibusDisplayDataInterface;

class OmnibusDisplayData implements OmnibusDisplayDataInterface
{
    public function __construct(
        private readonly float $lowestPrice,
        private readonly int $periodDays
    ) {
    }

    public function getLowestPrice(): float
    {
        return $this->lowestPrice;
    }

    public function getPeriodDays(): int
    {
        return $this->periodDays;
    }
}
