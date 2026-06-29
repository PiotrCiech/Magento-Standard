<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use Magento\Framework\Exception\LocalizedException;

class NipNormalizer
{
    public function normalize(string $nip): string
    {
        $digits = preg_replace('/\D+/', '', $nip) ?? '';

        if (!preg_match('/^\d{10}$/', $digits)) {
            throw new LocalizedException(__('Invalid NIP number.'));
        }

        return $digits;
    }
}
