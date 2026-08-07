<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class ReviewConfig
{
    private const XML_PATH_ENABLED = 'rokezzz_review/general/enabled';
    private const XML_PATH_GOOGLE_API_KEY = 'rokezzz_review/general/google_api_key';
    private const XML_PATH_GOOGLE_PLACE_ID = 'rokezzz_review/general/google_place_id';
    private const XML_PATH_CACHE_TTL_HOURS = 'rokezzz_review/general/cache_ttl_hours';
    private const XML_PATH_MAX_REVIEWS = 'rokezzz_review/general/max_reviews';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGoogleApiKey(?int $storeId = null): string
    {
        return trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getGooglePlaceId(?int $storeId = null): string
    {
        return trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_PLACE_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getCacheTtlHours(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_CACHE_TTL_HOURS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return max(1, $value);
    }

    public function getMaxReviews(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_REVIEWS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return max(1, $value);
    }

    public function isConfigured(?int $storeId = null): bool
    {
        return $this->getGoogleApiKey($storeId) !== ''
            && $this->getGooglePlaceId($storeId) !== '';
    }
}
