<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Exception\KsefApiException;

final class KsefInvoiceStatusProvider
{
    public function __construct(
        private readonly KsefHttpClient $httpClient
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $invoiceReferenceNumber, KsefState $state, ?int $storeId = null): array
    {
        $sessionReferenceNumber = $state->getSessionReferenceNumber();
        $accessToken = $state->getAccessToken();

        if (is_null($sessionReferenceNumber) || is_null($accessToken)) {
            throw new KsefApiException('KSeF state is incomplete for status lookup.');
        }

        return $this->httpClient->getJson(
            sprintf(KsefEndpoints::SESSION_INVOICE_STATUS, $sessionReferenceNumber, $invoiceReferenceNumber),
            ['Authorization' => 'Bearer ' . $accessToken],
            $storeId
        );
    }
}
