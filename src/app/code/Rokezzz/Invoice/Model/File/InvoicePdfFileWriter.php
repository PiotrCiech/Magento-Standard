<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\File;

use Rokezzz\Invoices\Api\InvoicePdfFileWriterInterface;
use Rokezzz\Invoices\Api\InvoicePdfPathResolverInterface;

class InvoicePdfFileWriter implements InvoicePdfFileWriterInterface
{
    public function __construct(
        private readonly InvoicePdfPathResolverInterface $pathResolver,
        private readonly VarDirectoryWriter $varDirectoryWriter
    ) {
    }

    public function exists(string $invoiceNumber, ?int $storeId = null): bool
    {
        return $this->varDirectoryWriter->exists(
            $this->pathResolver->getPdfRelativePath($invoiceNumber, $storeId)
        );
    }

    public function read(string $invoiceNumber, ?int $storeId = null): string
    {
        return $this->varDirectoryWriter->read(
            $this->pathResolver->getPdfRelativePath($invoiceNumber, $storeId)
        );
    }

    public function write(string $invoiceNumber, string $content, ?int $storeId = null): void
    {
        $this->varDirectoryWriter->write(
            $this->pathResolver->getPdfRelativePath($invoiceNumber, $storeId),
            $content
        );
    }

    public function delete(string $invoiceNumber, ?int $storeId = null): void
    {
        $this->varDirectoryWriter->delete(
            $this->pathResolver->getPdfRelativePath($invoiceNumber, $storeId)
        );
    }
}
