<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

final class KsefResponseParser
{
    /**
     * @param array<string, mixed> $response
     */
    public function getKsefNumber(array $response): ?string
    {
        $candidates = [
            $response['ksefNumber'] ?? null,
            $response['invoice']['ksefNumber'] ?? null,
            $response['processingResult']['ksefNumber'] ?? null,
            $response['invoiceStatus']['ksefReferenceNumber'] ?? null,
            $response['status']['extensions']['originalKsefNumber'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $response
     */
    public function getUpoDownloadUrl(array $response): ?string
    {
        $candidates = [
            $response['upoDownloadUrl'] ?? null,
            $response['invoice']['upoDownloadUrl'] ?? null,
            $response['processingResult']['upoDownloadUrl'] ?? null,
            $response['invoiceStatus']['upoDownloadUrl'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $response
     */
    public function getStatusCode(array $response): ?int
    {
        $status = $response['status'] ?? null;

        if (!is_array($status)) {
            return null;
        }

        return isset($status['code']) ? (int) $status['code'] : null;
    }

    /**
     * @param array<string, mixed> $response
     */
    public function isDuplicate(array $response): bool
    {
        return $this->getStatusCode($response) === 440;
    }
}
