<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

interface InvoiceXmlGeneratorInterface
{
    public function generate(InvoiceInterface $invoice): string;

    public function delete(InvoiceInterface $invoice): void;
}
