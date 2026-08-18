<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Model;

use DateTimeImmutable;
use Magento\Framework\Exception\LocalizedException;

final class DeliveryDateValidator
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function validate(?string $deliveryDate, ?int $storeId = null): ?string
    {
        if (is_null($deliveryDate) || trim($deliveryDate) === '') {
            if ($this->config->isRequired($storeId)) {
                throw new LocalizedException(__('Please select a preferred delivery date.'));
            }

            return null;
        }

        $deliveryDate = trim($deliveryDate);
        $parsedDate = DateTimeImmutable::createFromFormat('Y-m-d', $deliveryDate);

        if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $deliveryDate) {
            throw new LocalizedException(__('The preferred delivery date format is invalid.'));
        }

        $minDate = $this->config->getMinDate($storeId);
        $maxDate = $this->config->getMaxDate($storeId);

        if ($deliveryDate < $minDate || $deliveryDate > $maxDate) {
            throw new LocalizedException(__('The selected delivery date is outside the allowed range.'));
        }

        if ($this->config->isExcludeWeekends($storeId) && $this->isWeekend($parsedDate)) {
            throw new LocalizedException(__('Weekend dates cannot be selected as the delivery date.'));
        }

        return $deliveryDate;
    }

    private function isWeekend(DateTimeImmutable $date): bool
    {
        $dayOfWeek = (int) $date->format('N');

        return $dayOfWeek >= 6;
    }
}
