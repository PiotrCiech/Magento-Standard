<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoice\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoice\Api\KsefClientInterface;
use Rokezzz\Invoice\Exception\KsefApiException;
use Rokezzz\Invoice\Model\Ksef\KsefStatusPoller;

final class KsefInvoiceProcessor
{
    public function __construct(
        private readonly InvoiceXmlGeneratorInterface $invoiceXmlGenerator,
        private readonly InvoiceKsefStatusWriter $statusWriter,
        private readonly KsefStatusPoller $statusPoller,
        private readonly InvoiceUpoPersister $upoPersister
    ) {
    }

    public function process(InvoiceInterface $invoice, KsefClientInterface $client): void
    {
        $storeId = (int) $invoice->getStoreId();
        $invoiceNumber = (string) $invoice->getIncrementId();

        $this->statusWriter->update($invoice, KsefUpoStatus::Processing);

        $xmlContent = $this->invoiceXmlGenerator->generate($invoice);
        $sendResult = $client->sendInvoice($xmlContent, $storeId);
        $invoiceReferenceNumber = (string) ($sendResult['referenceNumber'] ?? '');

        if ($invoiceReferenceNumber === '') {
            throw new KsefApiException('KSeF did not return invoice reference number.');
        }

        $processingResult = $this->statusPoller->waitUntilProcessed(
            $client,
            $invoiceReferenceNumber,
            $storeId
        );

        $this->upoPersister->persist($invoiceNumber, $processingResult, $client, $storeId);

        $this->statusWriter->update(
            $invoice,
            KsefUpoStatus::Done,
            $processingResult->getKsefNumber()
        );
    }
}
