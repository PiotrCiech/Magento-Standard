<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Api;

interface InvoiceXmlFileWriterInterface
{
    public function exists(string $invoiceNumber, ?int $storeId = null): bool;

    public function read(string $invoiceNumber, ?int $storeId = null): string;

    public function write(string $invoiceNumber, string $content, ?int $storeId = null): void;

    public function delete(string $invoiceNumber, ?int $storeId = null): void;
}
