<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

interface InvoicePdfGeneratorInterface
{
    public function generate(InvoiceInterface $invoice): string;

    public function delete(InvoiceInterface $invoice): void;
}
