<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

final class CategoryLoader
{
    public function __construct(
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly CategoryHelper $categoryHelper,
    ) {
    }

    public function load(int $categoryId): ?Category
    {
        if ($categoryId <= 0) {
            return null;
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->setStoreId((int) $this->storeManager->getStore()->getId());
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', $categoryId);
        $collection->addIsActiveFilter();

        $category = $collection->getFirstItem();

        if (is_null($category->getId())) {
            return null;
        }

        if (!$this->categoryHelper->canShow($category)) {
            return null;
        }

        return $category;
    }
}
