<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Plugin\Catalog\Model\Layer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

final class AddNewsAttributesToCollectionPlugin
{
    /**
     * @param CollectionFilterInterface $subject
     * @param void $result
     * @param Collection $collection
     * @param Category $category
     */
    public function afterFilter(
        CollectionFilterInterface $subject,
        $result,
        $collection,
        Category $category
    ): void {
        $collection->addAttributeToSelect(['news_from_date', 'news_to_date']);
    }
}
