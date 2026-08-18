<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

use Rokezzz\Invoice\Api\KsefClientInterface;
use Rokezzz\Invoice\Api\UpoFileWriterInterface;
use Rokezzz\Invoice\Exception\KsefApiException;
use Rokezzz\Invoice\Model\Data\KsefProcessingResult;
use Rokezzz\Invoice\Model\Ksef\KsefUpoDownloader;
use Rokezzz\Invoice\Model\Ksef\KsefUpoRetryFetcher;

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
