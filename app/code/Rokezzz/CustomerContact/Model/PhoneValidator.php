<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model;

use Magento\Framework\Exception\LocalizedException;

final class PhoneValidator
{
    /**
     * @throws LocalizedException
     */
    public function normalizeOptional(string $phone): string
    {
        if (trim($phone) === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '48') && strlen($digits) === 11) {
            $digits = substr($digits, 2);
        }

        if (!preg_match('/^\d{9}$/', $digits)) {
            throw new LocalizedException(__('Please enter a valid phone number.'));
        }

        return $digits;
    }
}
