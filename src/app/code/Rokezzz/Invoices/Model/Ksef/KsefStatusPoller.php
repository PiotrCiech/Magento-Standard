<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Exception\KsefApiException;
use Rokezzz\Invoices\Model\Data\KsefProcessingResult;

class KsefStatusPoller
{
    private const MAX_ATTEMPTS = 120;
    private const SLEEP_SECONDS = 5;

    /** @var int[] */
    private const FAILURE_CODES = [405, 410, 415, 430, 435, 450, 500, 550];

    public function __construct(
        private readonly KsefResponseParser $responseParser
    ) {
    }

    public function waitUntilProcessed(
        KsefClientInterface $client,
        string $invoiceReferenceNumber,
        ?int $storeId = null
    ): KsefProcessingResult {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $status = $client->getSessionInvoiceStatus($invoiceReferenceNumber, $storeId);
            $statusCode = $this->responseParser->getStatusCode($status);

            if ($statusCode === 200) {
                $ksefNumber = $this->responseParser->getKsefNumber($status);
                $upoUrl = $this->responseParser->getUpoDownloadUrl($status);

                if ($ksefNumber !== null || $upoUrl !== null) {
                    return new KsefProcessingResult(
                        ksefNumber: $ksefNumber,
                        upoDownloadUrl: $upoUrl,
                        isDuplicate: false
                    );
                }
            }

            if ($statusCode === 440 || $this->responseParser->isDuplicate($status)) {
                return new KsefProcessingResult(
                    ksefNumber: $this->responseParser->getKsefNumber($status),
                    upoDownloadUrl: $this->responseParser->getUpoDownloadUrl($status),
                    isDuplicate: true
                );
            }

            if ($statusCode !== null && in_array($statusCode, self::FAILURE_CODES, true)) {
                $details = $status['status']['details'] ?? [];

                throw new KsefApiException(sprintf(
                    'Invoice failed in KSeF. Code=%d, details=%s',
                    $statusCode,
                    is_array($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : ''
                ));
            }

            if ($statusCode === 100 || $statusCode === 150) {
                sleep(self::SLEEP_SECONDS);
                continue;
            }

            sleep(self::SLEEP_SECONDS);
        }

        throw new KsefApiException('Timeout waiting for invoice processing status.');
    }
}
