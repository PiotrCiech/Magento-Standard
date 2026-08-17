<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Api;

interface InvoicePdfPathResolverInterface
{
    public function getPdfRelativePath(string $invoiceNumber, ?int $storeId = null): string;
}
