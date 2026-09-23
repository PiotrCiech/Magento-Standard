<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Omnibus\Api\Data\OmnibusDisplayDataInterface;
use Rokezzz\Omnibus\Api\OmnibusDisplayDataProviderInterface;
use Rokezzz\Omnibus\Model\Config;

class OmnibusPrice implements ArgumentInterface
{
    /** @var array<string, OmnibusDisplayDataInterface|null> */
    private array $displayDataCache = [];

    public function __construct(
        private readonly Config $config,
        private readonly OmnibusDisplayDataProviderInterface $displayDataProvider,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly Registry $registry,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function shouldRender(?ProductInterface $product, bool $listingContext): bool
    {
        $storeId = (int) $this->storeManager->getStore()->getId();

        if (!$listingContext && !$this->config->isShowOnPdp($storeId)) {
            return false;
        }

        return $this->getDisplayData($product) !== null;
    }

    public function getDisplayData(?ProductInterface $product): ?OmnibusDisplayDataInterface
    {
        $product = $this->resolveProduct($product);
        if ($product === null) {
            return null;
        }

        $storeId = (int) $this->storeManager->getStore()->getId();
        $cacheKey = $product->getId() . '_' . $storeId;

        if (!array_key_exists($cacheKey, $this->displayDataCache)) {
            $this->displayDataCache[$cacheKey] = $this->displayDataProvider->getDisplayData(
                $product,
                $storeId
            );
        }

        return $this->displayDataCache[$cacheKey];
    }

    public function getFormattedLowestPrice(?ProductInterface $product): string
    {
        $displayData = $this->getDisplayData($product);
        if ($displayData === null) {
            return '';
        }

        return $this->priceCurrency->format(
            $displayData->getLowestPrice(),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            (int) $this->storeManager->getStore()->getId()
        );
    }

    public function getPeriodDays(?ProductInterface $product): int
    {
        $displayData = $this->getDisplayData($product);

        return $displayData?->getPeriodDays()
            ?? $this->config->getPeriodDays((int) $this->storeManager->getStore()->getId());
    }

    private function resolveProduct(?ProductInterface $product): ?Product
    {
        if ($product instanceof Product) {
            return $product;
        }

        $registryProduct = $this->registry->registry('current_product');

        return $registryProduct instanceof Product ? $registryProduct : null;
    }
}
