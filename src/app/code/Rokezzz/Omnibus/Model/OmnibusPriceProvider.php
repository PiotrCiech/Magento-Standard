<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Omnibus\Api\Data\OmnibusDisplayDataInterface;
use Rokezzz\Omnibus\Api\GrossFinalPriceResolverInterface;
use Rokezzz\Omnibus\Api\OmnibusDisplayDataProviderInterface;
use Rokezzz\Omnibus\Model\Data\OmnibusDisplayData;
use Rokezzz\Omnibus\Model\ResourceModel\PriceHistory;

class OmnibusPriceProvider implements OmnibusDisplayDataProviderInterface
{
    private const PRICE_EPSILON = 0.0099;

    public function __construct(
        private readonly Config $config,
        private readonly GrossFinalPriceResolverInterface $priceResolver,
        private readonly PriceHistory $priceHistory,
        private readonly SimpleProductEligibility $productEligibility,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getDisplayData(
        ProductInterface $product,
        ?int $storeId = null
    ): ?OmnibusDisplayDataInterface {
        $storeId = $storeId ?? (int) $this->storeManager->getStore()->getId();

        if (!$this->isEligibleForDisplay($product, $storeId)) {
            return null;
        }

        $productId = (int) $product->getId();
        $currentPrice = $this->priceResolver->resolve($product, $storeId);
        $regularPrice = $this->priceResolver->resolveRegular($product, $storeId);

        if (!$this->isPromotionalPrice($currentPrice, $regularPrice)) {
            return null;
        }

        $periodDays = $this->config->getPeriodDays($storeId);
        $lowestPrice = $this->priceHistory->getMinimumPriceAbove(
            $productId,
            $storeId,
            $periodDays,
            $currentPrice
        ) ?? $regularPrice;

        if (!$this->hasMeaningfulLowestPrice($lowestPrice, $currentPrice)) {
            return null;
        }

        return new OmnibusDisplayData($lowestPrice, $periodDays);
    }

    private function isEligibleForDisplay(ProductInterface $product, int $storeId): bool
    {
        if (!$this->config->isEnabled($storeId)) {
            return false;
        }

        return $this->productEligibility->isEligible($product);
    }

    private function isPromotionalPrice(float $currentPrice, float $regularPrice): bool
    {
        return $currentPrice < ($regularPrice - self::PRICE_EPSILON);
    }

    private function hasMeaningfulLowestPrice(float $lowestPrice, float $currentPrice): bool
    {
        return $lowestPrice > ($currentPrice + self::PRICE_EPSILON);
    }
}
