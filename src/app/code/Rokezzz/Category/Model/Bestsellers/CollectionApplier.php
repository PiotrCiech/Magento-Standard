<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model\Bestsellers;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Zend_Db_Expr;

final class CollectionApplier
{
    public function __construct(
        private readonly BestsellersReportCollection $bestsellersReportCollection
    ) {
    }

    public function apply(Collection $collection): void
    {
        $productIds = $this->getProductIds();

        if ($productIds === []) {
            $collection->addFieldToFilter('entity_id', ['in' => [0]]);

            return;
        }

        $collection->addIdFilter($productIds);
        $collection->getSelect()->order(
            new Zend_Db_Expr('FIELD(e.entity_id,' . implode(',', $productIds) . ')')
        );
    }

    /**
     * @return int[]
     */
    private function getProductIds(): array
    {
        $productIds = [];
        $reportCollection = clone $this->bestsellersReportCollection;

        foreach ($reportCollection as $item) {
            $productIds[] = (int) $item->getProductId();
        }

        return array_values(array_unique($productIds));
    }
}
