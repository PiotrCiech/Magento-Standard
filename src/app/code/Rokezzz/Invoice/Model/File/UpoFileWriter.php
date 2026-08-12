<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\File;

use Rokezzz\Invoices\Api\InvoiceXmlPathResolverInterface;
use Rokezzz\Invoices\Api\UpoFileWriterInterface;

class UpoFileWriter implements UpoFileWriterInterface
{
    public function __construct(
        private readonly InvoiceXmlPathResolverInterface $pathResolver,
        private readonly VarDirectoryWriter $varDirectoryWriter
    ) {
    }

    public function exists(string $invoiceNumber, ?int $storeId = null): bool
    {
        return $this->varDirectoryWriter->exists(
            $this->pathResolver->getUpoRelativePath($invoiceNumber, $storeId)
        );
    }

    public function read(string $invoiceNumber, ?int $storeId = null): string
    {
        return $this->varDirectoryWriter->read(
            $this->pathResolver->getUpoRelativePath($invoiceNumber, $storeId)
        );
    }

    public function write(string $invoiceNumber, string $content, ?int $storeId = null): void
    {
        $this->varDirectoryWriter->write(
            $this->pathResolver->getUpoRelativePath($invoiceNumber, $storeId),
            $content
        );
    }
}
