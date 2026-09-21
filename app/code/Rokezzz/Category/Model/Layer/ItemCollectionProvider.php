<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model\Layer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;

final class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * @param Category $category
     */
    public function getCollection(Category $category): Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
