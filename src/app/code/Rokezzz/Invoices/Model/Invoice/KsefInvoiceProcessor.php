<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Api\UpoFileWriterInterface;
use Rokezzz\Invoices\Exception\KsefApiException;
use Rokezzz\Invoices\Model\Ksef\KsefStatusPoller;
use Rokezzz\Invoices\Model\Ksef\KsefUpoDownloader;

class KsefInvoiceProcessor
{
    private const MAX_UPO_ATTEMPTS = 12;
    private const UPO_SLEEP_SECONDS = 5;

    public function __construct(
        private readonly InvoiceXmlGeneratorInterface $invoiceXmlGenerator,
        private readonly InvoiceKsefStatusWriter $statusWriter,
        private readonly KsefStatusPoller $statusPoller,
        private readonly KsefUpoDownloader $upoDownloader,
        private readonly UpoFileWriterInterface $upoFileWriter
    ) {
    }

    public function process(InvoiceInterface $invoice, KsefClientInterface $client): void
    {
        $storeId = (int) $invoice->getStoreId();
        $invoiceNumber = (string) $invoice->getIncrementId();

        $this->statusWriter->update($invoice, KsefUpoStatus::Processing);

        try {
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

            $ksefNumber = $processingResult->getKsefNumber();
            $upoUrl = $processingResult->getUpoDownloadUrl();

            if ($ksefNumber === null && $upoUrl === null) {
                throw new KsefApiException('Invoice processed but no KSeF number or UPO URL returned.');
            }

            if ($upoUrl !== null) {
                $this->upoFileWriter->write(
                    $invoiceNumber,
                    $this->upoDownloader->downloadFromUrl($upoUrl),
                    $storeId
                );
            } elseif ($ksefNumber !== null) {
                $this->fetchAndSaveUpo($client, $invoice, $ksefNumber, $storeId);
            }

            $this->statusWriter->update($invoice, KsefUpoStatus::Done, $ksefNumber);
        } catch (KsefApiException $exception) {
            if ($exception->getStatusCode() === 440) {
                $this->statusWriter->update($invoice, KsefUpoStatus::Done);
                return;
            }

            throw $exception;
        }
    }

    private function fetchAndSaveUpo(
        KsefClientInterface $client,
        InvoiceInterface $invoice,
        string $ksefNumber,
        int $storeId
    ): void {
        $invoiceNumber = (string) $invoice->getIncrementId();

        for ($attempt = 1; $attempt <= self::MAX_UPO_ATTEMPTS; $attempt++) {
            try {
                $upoContent = $client->getUpo($ksefNumber, $storeId);
            } catch (KsefApiException) {
                $upoContent = '';
            }

            if (trim($upoContent) !== '') {
                $this->upoFileWriter->write($invoiceNumber, $upoContent, $storeId);
                return;
            }

            if ($attempt < self::MAX_UPO_ATTEMPTS) {
                sleep(self::UPO_SLEEP_SECONDS);
            }
        }

        throw new KsefApiException('Failed to fetch UPO after ' . self::MAX_UPO_ATTEMPTS . ' attempts.');
    }
}
