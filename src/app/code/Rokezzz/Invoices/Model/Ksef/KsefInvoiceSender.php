<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Exception\KsefApiException;

class KsefInvoiceSender
{
    public function __construct(
        private readonly KsefHttpClient $httpClient,
        private readonly KsefCrypto $crypto
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function send(string $xmlContent, KsefState $state, ?int $storeId = null): array
    {
        $sessionReferenceNumber = $state->getSessionReferenceNumber();
        $accessToken = $state->getAccessToken();
        $aesKey = $state->getAesKey();
        $aesIv = $state->getAesIv();

        if ($sessionReferenceNumber === null || $accessToken === null || $aesKey === null || $aesIv === null) {
            throw new KsefApiException('KSeF state is incomplete for invoice submission.');
        }

        $encryptedPayload = $this->crypto->encryptInvoicePayload($xmlContent, $aesKey, $aesIv);

        $response = $this->httpClient->postJsonWithHttpStatus(
            sprintf(KsefEndpoints::SESSIONS_ONLINE_INVOICES, $sessionReferenceNumber),
            ['Authorization' => 'Bearer ' . $accessToken],
            [
                'invoiceHash' => base64_encode(hash('sha256', $xmlContent, true)),
                'invoiceSize' => strlen($xmlContent),
                'encryptedInvoiceHash' => $encryptedPayload['encryptedInvoiceHash'],
                'encryptedInvoiceSize' => $encryptedPayload['encryptedInvoiceSize'],
                'encryptedInvoiceContent' => $encryptedPayload['encryptedInvoiceContent'],
                'offlineMode' => false,
            ],
            $storeId
        );

        if ($response['status'] === 202) {
            return $response['body'] ?? [];
        }

        throw new KsefApiException(
            sprintf('KSeF invoice send failed. HTTP %d: %s', $response['status'], $response['raw']),
            $response['status'],
            $response['raw']
        );
    }
}
