<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

enum KsefUpoStatus: string
{
    case Todo = 'todo';
    case Processing = 'processing';
    case Done = 'done';
    case Error = 'error';

    /**
     * @return string[]
     */
    public static function eligibleForSubmission(): array
    {
        return [
            self::Todo->value,
        ];
    }
}
