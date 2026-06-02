<?php

declare(strict_types=1);

namespace Rokezzz\GoogleAnalytics\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Rokezzz\GoogleAnalytics\Model\Config\Source\TrackingType;

class Config
{
    private const XML_PATH_TRACKING_TYPE = 'rokezzz_google_analytics/general/tracking_type';

    private const XML_PATH_TRACKING_ID = 'rokezzz_google_analytics/general/tracking_id';

    private const TRACKING_ID_PATTERN = '/^[A-Za-z0-9_-]+$/';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getTrackingType(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_TRACKING_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTrackingId(?int $storeId = null): string
    {
        $trackingId = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_TRACKING_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($trackingId === '' || !preg_match(self::TRACKING_ID_PATTERN, $trackingId)) {
            return '';
        }

        return $trackingId;
    }

    public function isGtagActive(?int $storeId = null): bool
    {
        $type = $this->getTrackingType($storeId);

        return in_array($type, [TrackingType::TYPE_GA4, TrackingType::TYPE_GTAG], true)
            && $this->getTrackingId($storeId) !== '';
    }

    public function isGtmActive(?int $storeId = null): bool
    {
        return $this->getTrackingType($storeId) === TrackingType::TYPE_GTM
            && $this->getTrackingId($storeId) !== '';
    }
}
