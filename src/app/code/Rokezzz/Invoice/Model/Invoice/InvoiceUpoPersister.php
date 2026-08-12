<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Api\UpoFileWriterInterface;
use Rokezzz\Invoices\Exception\KsefApiException;
use Rokezzz\Invoices\Model\Data\KsefProcessingResult;
use Rokezzz\Invoices\Model\Ksef\KsefUpoDownloader;
use Rokezzz\Invoices\Model\Ksef\KsefUpoRetryFetcher;

final class InvoiceUpoPersister
{
    public function __construct(
        private readonly UpoFileWriterInterface $upoFileWriter,
        private readonly KsefUpoDownloader $upoDownloader,
        private readonly KsefUpoRetryFetcher $upoRetryFetcher
    ) {
    }

    public function persist(
        string $invoiceNumber,
        KsefProcessingResult $processingResult,
        KsefClientInterface $client,
        ?int $storeId = null
    ): void {
        $upoUrl = $processingResult->getUpoDownloadUrl();
        if ($upoUrl !== null) {
            $this->upoFileWriter->write(
                $invoiceNumber,
                $this->upoDownloader->downloadFromUrl($upoUrl),
                $storeId
            );

            return;
        }

        $ksefNumber = $processingResult->getKsefNumber();
        if ($ksefNumber !== null) {
            $this->upoFileWriter->write(
                $invoiceNumber,
                $this->upoRetryFetcher->fetchUntilAvailable($client, $ksefNumber, $storeId),
                $storeId
            );

            return;
        }

        if ($processingResult->isDuplicate()) {
            return;
        }

        throw new KsefApiException('Invoice processed but no KSeF number or UPO URL returned.');
    }
}
