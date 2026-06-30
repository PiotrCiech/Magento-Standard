<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Zend_Db_Expr as DbExpr;

class Bestsellers extends ListProduct
{
    private BestsellersReportCollection $bestsellersReportCollection;
    private Visibility $productVisibility;

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        UrlHelper $urlHelper,
        BestsellersReportCollection $bestsellersReportCollection,
        Visibility $productVisibility,
        array $data = []
    ) {
        $this->bestsellersReportCollection = $bestsellersReportCollection;
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

    protected function _toHtml()
    {
        if (!$this->_scopeConfig->isSetFlag('rokezzz_settings/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return '';
        }
        return parent::_toHtml();
    }

    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $productIds = $this->getBestsellerProductIds();
            $this->_productCollection = $this->buildProductCollection($productIds);
        }

        return $this->_productCollection;
    }

    /**
     * @return int[]
     */
    private function getBestsellerProductIds(): array
    {
        $productIds = [];
        $reportCollection = clone $this->bestsellersReportCollection;

        foreach ($reportCollection as $item) {
            $productIds[] = (int) $item->getProductId();
        }

        return array_values(array_unique($productIds));
    }

    /**
     * @param int[] $productIds
     */
    private function buildProductCollection(array $productIds): ProductCollection
    {
        $collection = $this->getLayer()->getProductCollection();
        $collection->addAttributeToSelect('*');
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->addStoreFilter();

        if ($productIds === []) {
            $collection->addFieldToFilter('entity_id', ['in' => [0]]);
            return $collection;
        }

        $collection->addIdFilter($productIds);
        $collection->getSelect()->order(
            new DbExpr('FIELD(e.entity_id,' . implode(',', $productIds) . ')')
        );

        return $collection;
    }
}
