<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

enum LegalType: string
{
    case Standard = 'standard';
    case Correction = 'correction';
    case Advance = 'advance';
    case Proforma = 'proforma';

    /**
     * @return string[]
     */
    public static function ksefEligible(): array
    {
        return [
            self::Standard->value,
            self::Correction->value,
            self::Advance->value,
        ];
    }
}
