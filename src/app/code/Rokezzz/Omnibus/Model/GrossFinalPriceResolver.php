<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Customer\Model\Group;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config as TaxConfig;
use Rokezzz\Omnibus\Api\GrossFinalPriceResolverInterface;

class GrossFinalPriceResolver implements GrossFinalPriceResolverInterface
{
    public function __construct(
        private readonly TaxCalculationInterface $taxCalculation,
        private readonly TaxConfig $taxConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function resolve(ProductInterface $product, ?int $storeId = null): float
    {
        return $this->withStoreContext(
            $this->resolveStoreId($storeId),
            function (ProductInterface $product, int $storeId): float {
                /** @var Product $product */
                $product->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
                $finalPrice = (float) $product->getPriceInfo()
                    ->getPrice(FinalPrice::PRICE_CODE)
                    ->getValue();

                return $this->toGrossPrice($product, $finalPrice, $storeId);
            },
            $product
        );
    }

    public function resolveRegular(ProductInterface $product, ?int $storeId = null): float
    {
        return $this->withStoreContext(
            $this->resolveStoreId($storeId),
            function (ProductInterface $product, int $storeId): float {
                /** @var Product $product */
                return $this->toGrossPrice($product, (float) $product->getPrice(), $storeId);
            },
            $product
        );
    }

    /**
     * @param callable(ProductInterface, int): float $operation
     */
    private function withStoreContext(int $storeId, callable $operation, ProductInterface $product): float
    {
        $currentStoreId = (int) $this->storeManager->getStore()->getId();

        if ($currentStoreId !== $storeId) {
            $this->storeManager->setCurrentStore($storeId);
        }

        try {
            return $operation($product, $storeId);
        } finally {
            if ($currentStoreId !== $storeId) {
                $this->storeManager->setCurrentStore($currentStoreId);
            }
        }
    }

    private function resolveStoreId(?int $storeId): int
    {
        return $storeId ?? (int) $this->storeManager->getStore()->getId();
    }

    private function toGrossPrice(ProductInterface $product, float $price, int $storeId): float
    {
        if ($this->taxConfig->priceIncludesTax($storeId)) {
            return $price;
        }

        $taxClassAttribute = $product->getCustomAttribute('tax_class_id');
        $taxClassId = $taxClassAttribute !== null ? (int) $taxClassAttribute->getValue() : 0;
        $taxRate = (float) $this->taxCalculation->getCalculatedRate(
            $taxClassId,
            null,
            $storeId
        );

        return round($price * (1 + ($taxRate / 100)), 2);
    }
}
