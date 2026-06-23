<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Model\Invoice\LegalType;

class LegalTypeResolver
{
    public function resolve(InvoiceInterface $invoice): LegalType
    {
        $legalType = (string) $invoice->getData('rokezzz_invoice_legal_type');

        return LegalType::tryFrom($legalType) ?? LegalType::Standard;
    }
}
