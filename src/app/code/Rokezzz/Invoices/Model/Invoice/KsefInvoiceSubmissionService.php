<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Model\Data\KsefSubmissionResult;

final class KsefInvoiceSubmissionService
{
    public function __construct(
        private readonly PendingKsefInvoiceProvider $pendingInvoiceProvider,
        private readonly KsefStoreBatchSubmitter $storeBatchSubmitter
    ) {
    }

    public function execute(): KsefSubmissionResult
    {
        $invoices = $this->pendingInvoiceProvider->getPendingInvoices();

        if ($invoices === []) {
            return KsefSubmissionResult::empty();
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($this->groupInvoicesByStore($invoices) as $storeId => $storeInvoices) {
            $batchResult = $this->storeBatchSubmitter->submit($storeId, $storeInvoices);
            $success += $batchResult->getSuccess();
            $failed += $batchResult->getFailed();
            $errors = array_merge($errors, $batchResult->getErrors());
        }

        return new KsefSubmissionResult(
            total: count($invoices),
            success: $success,
            failed: $failed,
            errors: $errors
        );
    }

    /**
     * @param InvoiceInterface[] $invoices
     * @return array<int, InvoiceInterface[]>
     */
    private function groupInvoicesByStore(array $invoices): array
    {
        $grouped = [];

        foreach ($invoices as $invoice) {
            $storeId = (int) $invoice->getStoreId();
            $grouped[$storeId][] = $invoice;
        }

        return $grouped;
    }
}
