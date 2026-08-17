<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

final class ProductCollectionInitializer
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly Config $catalogConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly Visibility $productVisibility
    ) {
    }

    public function create(): Collection
    {
        $collection = $this->objectManager->create(Collection::class);
        $this->initialize($collection);

        return $collection;
    }

    public function initialize(Collection $collection): void
    {
        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToSelect(['news_from_date', 'news_to_date'])
            ->setStore($this->storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
    }
}
