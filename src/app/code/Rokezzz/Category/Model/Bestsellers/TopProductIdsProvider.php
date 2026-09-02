<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model\Bestsellers;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Magento\Store\Model\StoreManagerInterface;

final class TopProductIdsProvider
{
    private const CACHE_ID_PREFIX = 'rokezzz_category_top_bestseller_ids_';
    private const CACHE_LIFETIME = 3600;
    private const LIMIT = 10;

    public function __construct(
        private readonly BestsellersReportCollection $bestsellersReportCollection,
        private readonly CacheInterface $cache,
        private readonly SerializerInterface $serializer,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return int[]
     */
    public function getTopProductIds(): array
    {
        $cacheId = self::CACHE_ID_PREFIX . (int) $this->storeManager->getStore()->getId();

        $cached = $this->cache->load($cacheId);
        if ($cached !== false) {
            $decoded = $this->serializer->unserialize($cached);

            return is_array($decoded) ? array_map('intval', $decoded) : [];
        }

        $productIds = [];
        $collection = clone $this->bestsellersReportCollection;

        foreach ($collection as $item) {
            if (count($productIds) >= self::LIMIT) {
                break;
            }

            $productId = (int) $item->getProductId();
            if ($productId > 0) {
                $productIds[$productId] = $productId;
            }
        }

        $productIds = array_values($productIds);
        $this->cache->save($this->serializer->serialize($productIds), $cacheId, [], self::CACHE_LIFETIME);

        return $productIds;
    }
}
