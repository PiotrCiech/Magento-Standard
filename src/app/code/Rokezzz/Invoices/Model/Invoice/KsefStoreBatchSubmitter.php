<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Model\Data\KsefSubmissionBatchResult;
use Rokezzz\Invoices\Model\Ksef\KsefClientFactory;
use Throwable;

class KsefStoreBatchSubmitter
{
    public function __construct(
        private readonly KsefClientFactory $ksefClientFactory,
        private readonly KsefInvoiceProcessor $invoiceProcessor,
        private readonly KsefInvoiceFailureHandler $failureHandler,
        private readonly StuckProcessingInvoiceRecovery $stuckProcessingRecovery,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param InvoiceInterface[] $invoices
     */
    public function submit(int $storeId, array $invoices): KsefSubmissionBatchResult
    {
        if ($invoices === []) {
            return KsefSubmissionBatchResult::empty();
        }

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
                    $this->failureHandler->handle($invoice, $exception);
                    $failed++;
                    $errors[] = $this->failureHandler->formatErrorMessage($invoice, $exception);
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
                if ($this->failureHandler->isSubmitted($invoice)) {
                    continue;
                }

                $this->failureHandler->handle($invoice, $exception);
                $failed++;
            }

            $errors[] = sprintf('Store %d KSeF session failed: %s', $storeId, $exception->getMessage());
        } finally {
            $this->closeSessionSafely($client, $storeId);
        }

        return new KsefSubmissionBatchResult($success, $failed, $errors);
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
