<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Model;

use Magento\Framework\Exception\LocalizedException;

final class NipValidator
{
    /**
     * @var int[]
     */
    private const CHECKSUM_WEIGHTS = [6, 5, 7, 8, 3, 4, 5, 6, 7];

    /**
     * @throws LocalizedException
     */
    public function normalizeOptional(string $nip): string
    {
        if (trim($nip) === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $nip) ?? '';

        if (!preg_match('/^\d{10}$/', $digits) || !$this->hasValidChecksum($digits)) {
            throw new LocalizedException(__('Please enter a valid 10-digit NIP number.'));
        }

        return $digits;
    }

    private function hasValidChecksum(string $digits): bool
    {
        $sum = 0;

        foreach (self::CHECKSUM_WEIGHTS as $index => $weight) {
            $sum += ((int) $digits[$index]) * $weight;
        }

        $checksum = $sum % 11;

        if ($checksum === 10) {
            return false;
        }

        return $checksum === (int) $digits[9];
    }
}
