<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\File;

use Rokezzz\Invoices\Api\InvoicePdfPathResolverInterface;
use Rokezzz\Invoices\Api\InvoiceXmlPathResolverInterface;
use Rokezzz\Invoices\Model\Config;

class InvoicePdfPathResolver implements InvoicePdfPathResolverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly InvoiceXmlPathResolverInterface $xmlPathResolver
    ) {
    }

    public function getPdfRelativePath(string $invoiceNumber, ?int $storeId = null): string
    {
        $filename = $this->xmlPathResolver->sanitizeInvoiceNumber($invoiceNumber) . '.pdf';

        return trim($this->config->getInvoicesDirectory($storeId), '/') . '/' . $filename;
    }
}
