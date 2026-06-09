<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Omnibus\Api\GrossFinalPriceResolverInterface;
use Rokezzz\Omnibus\Api\PriceHistoryRecorderInterface;
use Rokezzz\Omnibus\Model\ResourceModel\PriceHistory;

class PriceHistoryRecorder implements PriceHistoryRecorderInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly GrossFinalPriceResolverInterface $priceResolver,
        private readonly PriceHistory $priceHistory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SimpleProductEligibility $productEligibility,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function recordForAllStores(ProductInterface $product): void
    {
        if (!$this->productEligibility->isEligible($product)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int) $store->getId();
            if (!$this->config->isEnabled($storeId)) {
                continue;
            }

            $this->recordForProduct((int) $product->getId(), $storeId);
        }
    }

    public function recordForProduct(int $productId, int $storeId): void
    {
        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        $product = $this->productRepository->getById($productId, false, $storeId);
        if (!$this->productEligibility->isEligible($product)) {
            return;
        }

        $price = $this->priceResolver->resolve($product, $storeId);
        $this->priceHistory->insert($productId, $storeId, $price);
    }
}
