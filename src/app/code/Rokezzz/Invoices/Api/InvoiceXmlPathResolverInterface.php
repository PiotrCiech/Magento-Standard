<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Api;

interface InvoiceXmlPathResolverInterface
{
    public function getXmlRelativePath(string $invoiceNumber, ?int $storeId = null): string;

    public function getUpoRelativePath(string $invoiceNumber, ?int $storeId = null): string;

    public function sanitizeInvoiceNumber(string $invoiceNumber): string;
}
