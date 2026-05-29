<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class Newest extends ListProduct
{
    public function __construct(
        private readonly Context $context,
        private readonly PostHelper $postDataHelper,
        private readonly Resolver $layerResolver,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly UrlHelper $urlHelper,
        private readonly Collection $productCollection,
        private readonly Visibility $productVisibility,
        array $data = []
    ) {
        $this->productCollection = $productCollection;
        $this->productVisibility = $productVisibility;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    protected function _getProductCollection()
    {
        if ($this->_productCollection !== null) {
            return $this->_productCollection;
        }

        /** @var Collection $collection */
        $collection = clone $this->productCollection;
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->addStoreFilter();
        $collection->setOrder('created_at', Collection::SORT_ORDER_DESC);

        $this->_productCollection = $collection;

        return $this->_productCollection;
    }
}
