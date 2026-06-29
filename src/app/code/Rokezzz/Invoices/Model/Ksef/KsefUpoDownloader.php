<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Magento\Framework\HTTP\Client\CurlFactory;
use Rokezzz\Invoices\Exception\KsefApiException;

class KsefUpoDownloader
{
    public function __construct(
        private readonly CurlFactory $curlFactory
    ) {
    }

    public function downloadFromUrl(string $url): string
    {
        $curl = $this->curlFactory->create();
        $curl->setHeaders(['Accept' => 'application/xml']);
        $curl->setTimeout(30);
        $curl->get($url);

        $statusCode = $curl->getStatus();
        $responseBody = (string) $curl->getBody();

        if ($statusCode !== 200) {
            throw new KsefApiException(
                sprintf('UPO download failed. HTTP %d: %s', $statusCode, $responseBody),
                $statusCode,
                $responseBody
            );
        }

        return $responseBody;
    }
}
