<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\View\Element\Template\Context;
use Rokezzz\DynamicCategories\Model\Bestsellers\CollectionApplier;
use Rokezzz\DynamicCategories\Model\ProductCollectionInitializer;

class Bestsellers extends ListProduct
{
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        UrlHelper $urlHelper,
        private readonly ProductCollectionInitializer $productCollectionInitializer,
        private readonly CollectionApplier $collectionApplier,
        array $data = []
    ) {
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
        if (!is_null($this->_productCollection)) {
            return $this->_productCollection;
        }

        $collection = $this->productCollectionInitializer->create();
        $this->collectionApplier->apply($collection);
        $this->_productCollection = $collection;

        return $this->_productCollection;
    }
}
