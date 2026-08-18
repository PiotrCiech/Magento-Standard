<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Ksef;

use Rokezzz\Invoice\Exception\KsefApiException;

final class KsefUpoFetcher
{
    public function __construct(
        private readonly KsefHttpClient $httpClient
    ) {
    }

    public function get(string $ksefNumber, KsefState $state, ?int $storeId = null): string
    {
        $sessionReferenceNumber = $state->getSessionReferenceNumber();
        $accessToken = $state->getAccessToken();

        if (is_null($sessionReferenceNumber) || is_null($accessToken)) {
            throw new KsefApiException('KSeF state is incomplete for UPO download.');
        }

        return $this->httpClient->getXml(
            sprintf(KsefEndpoints::SESSION_INVOICE_UPO, $sessionReferenceNumber, $ksefNumber),
            ['Authorization' => 'Bearer ' . $accessToken],
            $storeId
        );
    }

    public function tryGet(string $ksefNumber, KsefState $state, ?int $storeId = null): ?string
    {
        try {
            $upoContent = $this->get($ksefNumber, $state, $storeId);
        } catch (KsefApiException) {
            return null;
        }

        $upoContent = trim($upoContent);

        return $upoContent !== '' ? $upoContent : null;
    }
}
