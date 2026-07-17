<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Rokezzz\Review\Model\Config\ReviewConfig;

class GooglePlacesClient
{
    private const API_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    public function __construct(
        private readonly CurlFactory $curlFactory,
        private readonly Json $json,
        private readonly ReviewConfig $reviewConfig
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function fetch(?int $storeId = null): array
    {
        if (!$this->reviewConfig->isConfigured($storeId)) {
            throw new LocalizedException(__('Google reviews configuration is incomplete.'));
        }

        $query = http_build_query([
            'place_id' => $this->reviewConfig->getGooglePlaceId($storeId),
            'fields' => 'rating,user_ratings_total,reviews',
            'reviews_sort' => 'newest',
            'key' => $this->reviewConfig->getGoogleApiKey($storeId),
        ]);

        $curl = $this->curlFactory->create();
        $curl->setHeaders(['Accept' => 'application/json']);
        $curl->setTimeout(30);
        $curl->get(self::API_URL . '?' . $query);

        $statusCode = $curl->getStatus();
        $responseBody = (string) $curl->getBody();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new LocalizedException(
                __('Google Places request failed with HTTP status %1.', $statusCode)
            );
        }

        $decoded = $this->json->unserialize($responseBody);

        if (!is_array($decoded)) {
            throw new LocalizedException(__('Google Places returned an invalid JSON response.'));
        }

        $status = (string) ($decoded['status'] ?? '');
        if ($status !== '' && $status !== 'OK') {
            throw new LocalizedException(
                __('Google Places request failed with API status "%1".', $status)
            );
        }

        return $decoded;
    }
}
