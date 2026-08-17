<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\Invoice\Api\InvoiceXmlGeneratorInterface;
use Throwable;

final class KsefInvoiceFailureHandler
{
    public function __construct(
        private readonly InvoiceXmlGeneratorInterface $invoiceXmlGenerator,
        private readonly InvoiceKsefStatusWriter $statusWriter,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(InvoiceInterface $invoice, Throwable $exception): void
    {
        if ($this->isSubmitted($invoice)) {
            return;
        }

        $this->invoiceXmlGenerator->delete($invoice);
        $this->statusWriter->update($invoice, KsefUpoStatus::Todo);

        $this->logger->error('KSeF invoice processing failed', [
            'invoice_id' => $invoice->getEntityId(),
            'invoice_number' => $invoice->getIncrementId(),
            'error' => $exception->getMessage(),
        ]);
    }

    public function formatErrorMessage(InvoiceInterface $invoice, Throwable $exception): string
    {
        return sprintf(
            'Invoice %s: %s',
            (string) $invoice->getIncrementId(),
            $exception->getMessage()
        );
    }

    public function isSubmitted(InvoiceInterface $invoice): bool
    {
        return (string) $invoice->getData('rokezzz_ksef_upo_status') === KsefUpoStatus::Done->value;
    }
}
