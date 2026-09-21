<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

enum PaymentMethod: string
{
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case PaidInAdvance = 'paid_in_advance';
}
