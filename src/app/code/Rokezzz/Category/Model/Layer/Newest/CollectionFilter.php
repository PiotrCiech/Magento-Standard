<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model\Layer\Newest;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Rokezzz\Category\Model\Newest\NewsDateFilter;
use Rokezzz\Category\Model\ProductCollectionInitializer;

final class CollectionFilter implements CollectionFilterInterface
{
    public function __construct(
        private readonly ProductCollectionInitializer $productCollectionInitializer,
        private readonly NewsDateFilter $newsDateFilter
    ) {
    }

    /**
     * @param Collection $collection
     * @param Category $category
     */
    public function filter($collection, Category $category): void
    {
        $this->productCollectionInitializer->initialize($collection);
        $this->newsDateFilter->apply($collection);
    }
}
