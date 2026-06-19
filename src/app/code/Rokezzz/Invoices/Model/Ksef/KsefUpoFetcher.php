<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Exception\KsefApiException;

class KsefUpoFetcher
{
    public function __construct(
        private readonly KsefHttpClient $httpClient
    ) {
    }

    public function get(string $ksefNumber, KsefState $state, ?int $storeId = null): string
    {
        $sessionReferenceNumber = $state->getSessionReferenceNumber();
        $accessToken = $state->getAccessToken();

        if ($sessionReferenceNumber === null || $accessToken === null) {
            throw new KsefApiException('KSeF state is incomplete for UPO download.');
        }

        return $this->httpClient->getXml(
            sprintf(KsefEndpoints::SESSION_INVOICE_UPO, $sessionReferenceNumber, $ksefNumber),
            ['Authorization' => 'Bearer ' . $accessToken],
            $storeId
        );
    }
}
