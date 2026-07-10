<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\Serializer\Json;

class ReviewCacheManager
{
    private const CACHE_KEY_PREFIX = 'rokezzz_review_google_reviews_';
    private const CACHE_TAG = 'ROKEZZZ_REVIEW_GOOGLE_REVIEWS';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly Json $json
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function save(array $payload, int $storeId): void
    {
        $this->cache->save(
            $this->json->serialize($payload),
            $this->getCacheKey($storeId),
            [self::CACHE_TAG]
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function load(int $storeId): ?array
    {
        $rawPayload = $this->cache->load($this->getCacheKey($storeId));
        if (!is_string($rawPayload) || $rawPayload === '') {
            return null;
        }

        $decoded = $this->json->unserialize($rawPayload);

        return is_array($decoded) ? $decoded : null;
    }

    public function isFresh(?array $payload, int $ttlHours): bool
    {
        if (!is_array($payload)) {
            return false;
        }

        $lastUpdatedAt = (string) ($payload['last_updated_at'] ?? '');
        if ($lastUpdatedAt === '') {
            return false;
        }

        $timestamp = strtotime($lastUpdatedAt);
        if ($timestamp === false) {
            return false;
        }

        return $timestamp >= (time() - ($ttlHours * 3600));
    }

    private function getCacheKey(int $storeId): string
    {
        return self::CACHE_KEY_PREFIX . $storeId;
    }
}
