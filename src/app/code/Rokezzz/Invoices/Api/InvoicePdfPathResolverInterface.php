<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Api;

interface InvoicePdfPathResolverInterface
{
    public function getPdfRelativePath(string $invoiceNumber, ?int $storeId = null): string;
}
