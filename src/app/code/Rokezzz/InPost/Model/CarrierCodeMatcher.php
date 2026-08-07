<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Model;

final class CarrierCodeMatcher
{
    private const INPOST_CARRIER_PREFIX = 'inpost';

    public function isInpostCarrier(string $carrierCode): bool
    {
        return str_starts_with(strtolower($carrierCode), self::INPOST_CARRIER_PREFIX);
    }
}
