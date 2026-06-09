<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Api\Data;

interface OmnibusDisplayDataInterface
{
    public function getLowestPrice(): float;

    public function getPeriodDays(): int;
}
