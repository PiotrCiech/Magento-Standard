<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class InvoiceKsefStatusWriter
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {
    }

    public function update(
        InvoiceInterface $invoice,
        KsefUpoStatus $status,
        ?string $ksefNumber = null
    ): void {
        $invoice->setData('rokezzz_ksef_upo_status', $status->value);

        if ($ksefNumber !== null) {
            $invoice->setData('rokezzz_ksef_number', $ksefNumber);
        }

        $this->invoiceRepository->save($invoice);
    }
}
