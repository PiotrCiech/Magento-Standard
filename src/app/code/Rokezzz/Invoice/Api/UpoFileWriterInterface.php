<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Api;

interface UpoFileWriterInterface
{
    public function exists(string $invoiceNumber, ?int $storeId = null): bool;

    public function read(string $invoiceNumber, ?int $storeId = null): string;

    public function write(string $invoiceNumber, string $content, ?int $storeId = null): void;
}
