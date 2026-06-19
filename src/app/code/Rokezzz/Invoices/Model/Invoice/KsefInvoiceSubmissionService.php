<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\Invoices\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Model\Data\KsefSubmissionResult;
use Rokezzz\Invoices\Model\Ksef\KsefClientFactory;
use Throwable;

class KsefInvoiceSubmissionService
{
    public function __construct(
        private readonly PendingKsefInvoiceProvider $pendingInvoiceProvider,
        private readonly KsefInvoiceProcessor $invoiceProcessor,
        private readonly KsefClientFactory $ksefClientFactory,
        private readonly InvoiceXmlGeneratorInterface $invoiceXmlGenerator,
        private readonly InvoiceKsefStatusWriter $statusWriter,
        private readonly StuckProcessingInvoiceRecovery $stuckProcessingRecovery,
        private readonly LoggerInterface $logger
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
            $batchResult = $this->submitStoreInvoices($storeId, $storeInvoices);
            $success += $batchResult['success'];
            $failed += $batchResult['failed'];
            $errors = array_merge($errors, $batchResult['errors']);
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
     * @return array{success: int, failed: int, errors: string[]}
     */
    private function submitStoreInvoices(int $storeId, array $invoices): array
    {
        $client = $this->ksefClientFactory->create();
        $success = 0;
        $failed = 0;
        $errors = [];

        try {
            $client->authenticate($storeId);
            $client->createSession($storeId);

            foreach ($invoices as $invoice) {
                try {
                    $this->invoiceProcessor->process($invoice, $client);
                    $success++;
                } catch (Throwable $exception) {
                    $this->handleInvoiceFailure($invoice, $exception);
                    $failed++;
                    $errors[] = sprintf(
                        'Invoice %s: %s',
                        (string) $invoice->getIncrementId(),
                        $exception->getMessage()
                    );
                }
            }

            $stuckCount = $this->stuckProcessingRecovery->resetStuckInvoices($invoices);
            if ($stuckCount > 0) {
                $failed += $stuckCount;
                $errors[] = sprintf(
                    'Safeguard: %d invoice(s) left in processing were reset to todo',
                    $stuckCount
                );
            }
        } catch (Throwable $exception) {
            foreach ($invoices as $invoice) {
                $this->handleInvoiceFailure($invoice, $exception);
            }

            $failed += count($invoices);
            $errors[] = sprintf('Store %d KSeF session failed: %s', $storeId, $exception->getMessage());
        } finally {
            $this->closeSessionSafely($client, $storeId);
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
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

    private function handleInvoiceFailure(InvoiceInterface $invoice, Throwable $exception): void
    {
        $this->invoiceXmlGenerator->delete($invoice);
        $this->statusWriter->update($invoice, KsefUpoStatus::Todo);

        $this->logger->error('KSeF invoice processing failed', [
            'invoice_id' => $invoice->getEntityId(),
            'invoice_number' => $invoice->getIncrementId(),
            'error' => $exception->getMessage(),
        ]);
    }

    private function closeSessionSafely(KsefClientInterface $client, int $storeId): void
    {
        try {
            $client->closeSession($storeId);
        } catch (Throwable $exception) {
            $this->logger->warning('KSeF session close failed', [
                'store_id' => $storeId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
