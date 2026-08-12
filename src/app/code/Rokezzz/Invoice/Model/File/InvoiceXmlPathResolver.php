<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\File;

use Rokezzz\Invoices\Api\InvoiceXmlPathResolverInterface;
use Rokezzz\Invoices\Model\Config;

class InvoiceXmlPathResolver implements InvoiceXmlPathResolverInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getXmlRelativePath(string $invoiceNumber, ?int $storeId = null): string
    {
        $filename = $this->sanitizeInvoiceNumber($invoiceNumber) . '.xml';

        return $this->getInvoicesDirectory($storeId) . '/' . $filename;
    }

    public function getUpoRelativePath(string $invoiceNumber, ?int $storeId = null): string
    {
        $filename = $this->sanitizeInvoiceNumber($invoiceNumber) . '_upo.xml';

        return $this->getInvoicesDirectory($storeId) . '/' . $filename;
    }

    public function sanitizeInvoiceNumber(string $invoiceNumber): string
    {
        return preg_replace('/[\/\\\\]/', '_', $invoiceNumber) ?? $invoiceNumber;
    }

    private function getInvoicesDirectory(?int $storeId): string
    {
        return trim($this->config->getInvoicesDirectory($storeId), '/');
    }
}
