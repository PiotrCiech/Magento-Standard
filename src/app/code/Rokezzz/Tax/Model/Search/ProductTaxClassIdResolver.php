<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Search;

use Magento\Catalog\Model\Product;
use Rokezzz\Tax\Model\ProductCollectionProvider;

class ProductTaxClassIdResolver
{
    public function __construct(
        private readonly ProductCollectionProvider $productCollectionProvider,
        private readonly DisplayPriceConverter $displayPriceConverter
    ) {
    }

    /**
     * @param int[] $productIds
     * @return array<int, int>
     */
    public function resolve(array $productIds, int $storeId): array
    {
        if ($productIds === []) {
            return [];
        }

        $collection = $this->productCollectionProvider->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('tax_class_id');
        $collection->addAttributeToFilter('entity_id', ['in' => $productIds]);

        $taxClassIds = [];
        $defaultTaxClassId = $this->displayPriceConverter->getDefaultTaxClassId($storeId);

        foreach ($collection as $product) {
            if (!$product instanceof Product) {
                continue;
            }

            $taxClassId = (int) $product->getTaxClassId();
            if ($taxClassId <= 0) {
                $taxClassId = $defaultTaxClassId;
            }

            $taxClassIds[(int) $product->getId()] = $taxClassId;
        }

        return $taxClassIds;
    }
}
