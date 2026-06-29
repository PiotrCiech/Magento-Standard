<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Api\KsefClientInterface;
use Rokezzz\Invoices\Exception\KsefApiException;

class KsefUpoRetryFetcher
{
    private const MAX_ATTEMPTS = 12;
    private const SLEEP_SECONDS = 5;

    public function fetchUntilAvailable(
        KsefClientInterface $client,
        string $ksefNumber,
        ?int $storeId = null
    ): string {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $upoContent = $client->tryGetUpo($ksefNumber, $storeId);

            if ($upoContent !== null) {
                return $upoContent;
            }

            if ($attempt < self::MAX_ATTEMPTS) {
                sleep(self::SLEEP_SECONDS);
            }
        }

        throw new KsefApiException('Failed to fetch UPO after ' . self::MAX_ATTEMPTS . ' attempts.');
    }
}
