<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

use Rokezzz\Invoice\Model\InvoiceCollectionCreator;
use Magento\Sales\Api\Data\InvoiceInterface;

final class StuckProcessingInvoiceRecovery
{
    public function __construct(
        private readonly InvoiceCollectionCreator $invoiceCollectionCreator,
        private readonly InvoiceKsefStatusWriter $statusWriter
    ) {
    }

    /**
     * @param InvoiceInterface[] $processedInvoices
     */
    public function resetStuckInvoices(array $processedInvoices): int
    {
        $invoiceIds = array_values(array_filter(array_map(
            static fn (InvoiceInterface $invoice): int => (int) $invoice->getEntityId(),
            $processedInvoices
        )));

        if ($invoiceIds === []) {
            return 0;
        }

        $collection = $this->invoiceCollectionCreator->create();
        $collection->addFieldToFilter('entity_id', ['in' => $invoiceIds]);
        $collection->addFieldToFilter('rokezzz_ksef_upo_status', ['eq' => KsefUpoStatus::Processing->value]);

        $resetCount = 0;

        foreach ($collection as $invoice) {
            if (!$invoice instanceof InvoiceInterface) {
                continue;
            }

            $this->statusWriter->update($invoice, KsefUpoStatus::Todo);
            $resetCount++;
        }

        return $resetCount;
    }
}
