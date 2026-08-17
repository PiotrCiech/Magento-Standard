<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Ksef;

use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Rokezzz\Invoice\Exception\KsefApiException;

final class KsefHttpClient
{
    public function __construct(
        private readonly CurlFactory $curlFactory,
        private readonly Json $json,
        private readonly KsefConfigProvider $configProvider
    ) {
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function postJson(string $endpoint, array $headers, array $payload, ?int $storeId = null): array
    {
        $responseBody = $this->request(
            'POST',
            $endpoint,
            array_merge(
                ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                $headers
            ),
            $payload === [] ? '{}' : $this->json->serialize($payload),
            $storeId
        );

        return $this->decodeJson($responseBody);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function getJson(string $endpoint, array $headers = [], ?int $storeId = null): array
    {
        $responseBody = $this->request(
            'GET',
            $endpoint,
            array_merge(['Accept' => 'application/json'], $headers),
            null,
            $storeId
        );

        return $this->decodeJson($responseBody);
    }

    /**
     * @param array<string, string> $headers
     */
    public function getXml(string $endpoint, array $headers = [], ?int $storeId = null): string
    {
        return $this->request(
            'GET',
            $endpoint,
            array_merge(['Accept' => 'application/xml'], $headers),
            null,
            $storeId
        );
    }

    /**
     * @param array<string, string> $headers
     */
    private function request(
        string $method,
        string $endpoint,
        array $headers,
        ?string $body,
        ?int $storeId
    ): string {
        $curl = $this->curlFactory->create();
        $curl->setHeaders($headers);
        $curl->setTimeout(30);

        $url = $this->configProvider->getApiUrl($storeId) . $endpoint;

        if ($method === 'GET') {
            $curl->get($url);
        } else {
            $curl->post($url, $body ?? '');
        }

        $statusCode = $curl->getStatus();
        $responseBody = (string) $curl->getBody();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new KsefApiException(
                sprintf('KSeF API request failed. HTTP %d: %s', $statusCode, $responseBody),
                $statusCode,
                $responseBody
            );
        }

        return $responseBody;
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed> $payload
     * @return array{status: int, body: array<string, mixed>|null, raw: string}
     */
    public function postJsonWithHttpStatus(
        string $endpoint,
        array $headers,
        array $payload,
        ?int $storeId = null
    ): array {
        $curl = $this->curlFactory->create();
        $curl->setHeaders(array_merge(
            ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            $headers
        ));
        $curl->setTimeout(30);

        $url = $this->configProvider->getApiUrl($storeId) . $endpoint;
        $curl->post($url, $payload === [] ? '{}' : $this->json->serialize($payload));

        $statusCode = $curl->getStatus();
        $responseBody = (string) $curl->getBody();
        $decodedBody = null;

        if ($responseBody !== '') {
            $decoded = $this->json->unserialize($responseBody);
            if (is_array($decoded)) {
                $decodedBody = $decoded;
            }
        }

        return [
            'status' => $statusCode,
            'body' => $decodedBody,
            'raw' => $responseBody,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $responseBody): array
    {
        $decoded = $this->json->unserialize($responseBody);

        if (!is_array($decoded)) {
            throw new KsefApiException('Unexpected non-array JSON response from KSeF API.');
        }

        return $decoded;
    }
}
