<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Model\Layer\Bestsellers;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Rokezzz\DynamicCategories\Model\Bestsellers\CollectionApplier;
use Rokezzz\DynamicCategories\Model\ProductCollectionInitializer;

final class CollectionFilter implements CollectionFilterInterface
{
    public function __construct(
        private readonly ProductCollectionInitializer $productCollectionInitializer,
        private readonly CollectionApplier $collectionApplier
    ) {
    }

    /**
     * @param Collection $collection
     * @param Category $category
     */
    public function filter($collection, Category $category): void
    {
        $this->productCollectionInitializer->initialize($collection);
        $this->collectionApplier->apply($collection);
    }
}
