<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\Model\Bestsellers;

use ArrayIterator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Bestsellers\CollectionApplier;

final class CollectionApplierTest extends TestCase
{
    public function testApplyFiltersEmptySetWhenReportHasNoProducts(): void
    {
        $report = $this->createMock(BestsellersReportCollection::class);
        $report->method('getIterator')->willReturn(new ArrayIterator([]));

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('entity_id', ['in' => [0]]);
        $collection->expects($this->never())->method('addIdFilter');

        (new CollectionApplier($report))->apply($collection);
    }

    public function testApplyKeepsReportOrderAndUniqueIds(): void
    {
        $first = new DataObject(['product_id' => 4]);
        $duplicate = new DataObject(['product_id' => 4]);
        $second = new DataObject(['product_id' => 9]);

        $report = $this->createMock(BestsellersReportCollection::class);
        $report->method('getIterator')->willReturn(new ArrayIterator([$first, $duplicate, $second]));

        $select = $this->createMock(Select::class);
        $select->expects($this->once())->method('order');

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addIdFilter')->with([4, 9]);
        $collection->method('getSelect')->willReturn($select);

        (new CollectionApplier($report))->apply($collection);
    }
}
