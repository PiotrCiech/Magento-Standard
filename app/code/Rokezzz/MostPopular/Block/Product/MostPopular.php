<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Rokezzz\MostPopular\Model\Config;
use Zend_Db_Expr as DbExpr;

class MostPopular extends ListProduct
{
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        UrlHelper $urlHelper,
        private readonly BestsellersReportCollection $bestsellersReportCollection,
        private readonly Visibility $productVisibility,
        private readonly Config $config,
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

    public function isEnabled(): bool
    {
        return $this->config->isEnabled((int) $this->_storeManager->getStore()->getId());
    }

    public function getProductsCount(): int
    {
        return $this->config->getProductsCount((int) $this->_storeManager->getStore()->getId());
    }

    public function getPeriod(): string
    {
        return $this->config->getPeriod((int) $this->_storeManager->getStore()->getId());
    }

    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $productIds = $this->getPopularProductIds();
            $this->_productCollection = $this->buildProductCollection($productIds);
        }

        return $this->_productCollection;
    }

    /**
     * @return int[]
     */
    private function getPopularProductIds(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $productIds = [];
        $reportCollection = clone $this->bestsellersReportCollection;
        $reportCollection->setPeriod($this->getPeriod());
        $limit = $this->getProductsCount();

        foreach ($reportCollection as $item) {
            if (count($productIds) >= $limit) {
                break;
            }

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
