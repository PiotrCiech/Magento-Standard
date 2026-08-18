<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Model;

use DateTimeImmutable;
use IntlDateFormatter;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

final class DeliveryDateFormatter
{
    public function __construct(
        private readonly TimezoneInterface $timezone
    ) {
    }

    public function formatForDisplay(?string $deliveryDate): string
    {
        if (is_null($deliveryDate) || trim($deliveryDate) === '') {
            return '';
        }

        $parsedDate = DateTimeImmutable::createFromFormat('Y-m-d', trim($deliveryDate));

        if ($parsedDate === false) {
            return trim($deliveryDate);
        }

        return $this->timezone->formatDate($parsedDate, IntlDateFormatter::MEDIUM, false);
    }
}
