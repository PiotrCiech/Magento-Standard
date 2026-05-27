<?php

declare(strict_types=1);

namespace Rokezzz\CustomerNip\Model;

class NipValidator
{
    public function isValid(string $nip): bool
    {
        $normalizedNip = preg_replace('/\D+/', '', $nip) ?? '';

        if (strlen($normalizedNip) !== 10) {
            return false;
        }

        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;

        for ($index = 0; $index < 9; $index++) {
            $sum += ((int) $normalizedNip[$index]) * $weights[$index];
        }

        $checksum = $sum % 11;
        if ($checksum === 10) {
            return false;
        }

        return $checksum === (int) $normalizedNip[9];
    }
}
