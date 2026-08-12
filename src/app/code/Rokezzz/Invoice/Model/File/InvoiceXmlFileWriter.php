<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\File;

use Rokezzz\Invoices\Api\InvoiceXmlFileWriterInterface;
use Rokezzz\Invoices\Api\InvoiceXmlPathResolverInterface;

class InvoiceXmlFileWriter implements InvoiceXmlFileWriterInterface
{
    public function __construct(
        private readonly InvoiceXmlPathResolverInterface $pathResolver,
        private readonly VarDirectoryWriter $varDirectoryWriter
    ) {
    }

    public function exists(string $invoiceNumber, ?int $storeId = null): bool
    {
        return $this->varDirectoryWriter->exists(
            $this->pathResolver->getXmlRelativePath($invoiceNumber, $storeId)
        );
    }

    public function read(string $invoiceNumber, ?int $storeId = null): string
    {
        return $this->varDirectoryWriter->read(
            $this->pathResolver->getXmlRelativePath($invoiceNumber, $storeId)
        );
    }

    public function write(string $invoiceNumber, string $content, ?int $storeId = null): void
    {
        $this->varDirectoryWriter->write(
            $this->pathResolver->getXmlRelativePath($invoiceNumber, $storeId),
            $content
        );
    }

    public function delete(string $invoiceNumber, ?int $storeId = null): void
    {
        $this->varDirectoryWriter->delete(
            $this->pathResolver->getXmlRelativePath($invoiceNumber, $storeId)
        );
    }
}
