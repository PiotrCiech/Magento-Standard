<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\Review\Api\Data\GoogleReviewInterface;
use Rokezzz\Review\Api\Data\GoogleReviewsInterface;
use Rokezzz\Review\Api\ReviewProviderInterface;
use Rokezzz\Review\Model\Config\ReviewConfig;
use Rokezzz\Review\Model\Data\GoogleReview;
use Rokezzz\Review\Model\Data\GoogleReviews;

class ReviewProvider implements ReviewProviderInterface
{
    public function __construct(
        private readonly ReviewConfig $reviewConfig,
        private readonly GooglePlacesClient $googlePlacesClient,
        private readonly ReviewCacheManager $reviewCacheManager,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function get(?int $storeId = null): GoogleReviewsInterface
    {
        $resolvedStoreId = $this->resolveStoreId($storeId);
        if (!$this->reviewConfig->isEnabled($resolvedStoreId)) {
            return $this->createEmptyResult($resolvedStoreId);
        }

        $cachedPayload = $this->reviewCacheManager->load($resolvedStoreId);
        if ($this->reviewCacheManager->isFresh(
            $cachedPayload,
            $this->reviewConfig->getCacheTtlHours($resolvedStoreId)
        )) {
            return $this->hydrate($resolvedStoreId, $cachedPayload);
        }

        try {
            return $this->refresh($resolvedStoreId);
        } catch (LocalizedException $exception) {
            $this->logger->warning(
                sprintf(
                    'Failed to refresh Google reviews for store %d: %s',
                    $resolvedStoreId,
                    $exception->getMessage()
                )
            );

            if (is_array($cachedPayload)) {
                return $this->hydrate($resolvedStoreId, $cachedPayload);
            }

            return $this->createEmptyResult($resolvedStoreId);
        }
    }

    public function refresh(?int $storeId = null): GoogleReviewsInterface
    {
        $resolvedStoreId = $this->resolveStoreId($storeId);
        if (!$this->reviewConfig->isEnabled($resolvedStoreId)) {
            return $this->createEmptyResult($resolvedStoreId);
        }

        $payload = $this->buildPayload(
            $this->googlePlacesClient->fetch($resolvedStoreId),
            $resolvedStoreId
        );
        $this->reviewCacheManager->save($payload, $resolvedStoreId);

        return $this->hydrate($resolvedStoreId, $payload);
    }

    /**
     * @param array<string, mixed> $apiResponse
     * @return array<string, mixed>
     */
    private function buildPayload(array $apiResponse, int $storeId): array
    {
        $result = isset($apiResponse['result']) && is_array($apiResponse['result'])
            ? $apiResponse['result']
            : [];
        $rawReviews = isset($result['reviews']) && is_array($result['reviews'])
            ? $result['reviews']
            : [];

        $items = [];
        $maxReviews = $this->reviewConfig->getMaxReviews($storeId);

        foreach (array_slice($rawReviews, 0, $maxReviews) as $review) {
            if (!is_array($review)) {
                continue;
            }

            $items[] = [
                'author_name' => trim((string) ($review['author_name'] ?? '')),
                'rating' => (int) ($review['rating'] ?? 0),
                'text' => trim((string) ($review['text'] ?? '')),
                'relative_time_description' => trim((string) ($review['relative_time_description'] ?? '')),
                'review_timestamp' => (int) ($review['time'] ?? 0),
            ];
        }

        return [
            'place_id' => $this->reviewConfig->getGooglePlaceId($storeId),
            'average_rating' => round((float) ($result['rating'] ?? 0.0), 2),
            'review_count' => (int) ($result['user_ratings_total'] ?? 0),
            'last_updated_at' => gmdate('c'),
            'items' => $items,
        ];
    }

    /**
     * @param array<string, mixed>|null $payload
     */
    private function hydrate(int $storeId, ?array $payload): GoogleReviewsInterface
    {
        if (!is_array($payload)) {
            return $this->createEmptyResult($storeId);
        }

        $items = [];
        $rawItems = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
        foreach ($rawItems as $item) {
            if (!is_array($item)) {
                continue;
            }

            $items[] = $this->hydrateItem($item);
        }

        return new GoogleReviews(
            $this->reviewConfig->isEnabled($storeId),
            (string) ($payload['place_id'] ?? $this->reviewConfig->getGooglePlaceId($storeId)),
            (float) ($payload['average_rating'] ?? 0.0),
            (int) ($payload['review_count'] ?? 0),
            isset($payload['last_updated_at']) ? (string) $payload['last_updated_at'] : null,
            $items
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    private function hydrateItem(array $item): GoogleReviewInterface
    {
        return new GoogleReview(
            (string) ($item['author_name'] ?? ''),
            (int) ($item['rating'] ?? 0),
            (string) ($item['text'] ?? ''),
            (string) ($item['relative_time_description'] ?? ''),
            (int) ($item['review_timestamp'] ?? 0)
        );
    }

    private function createEmptyResult(int $storeId): GoogleReviewsInterface
    {
        return new GoogleReviews(
            $this->reviewConfig->isEnabled($storeId),
            $this->reviewConfig->getGooglePlaceId($storeId),
            0.0,
            0,
            null,
            []
        );
    }

    private function resolveStoreId(?int $storeId): int
    {
        if ($storeId !== null) {
            return $storeId;
        }

        return (int) $this->storeManager->getStore()->getId();
    }
}
