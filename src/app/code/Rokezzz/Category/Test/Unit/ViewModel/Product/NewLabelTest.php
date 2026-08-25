<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\ViewModel\Product;

use DateTime;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Newest\NewsDateFilter;
use Rokezzz\Category\ViewModel\Product\NewLabel;

final class NewLabelTest extends TestCase
{
    public function testIsNewDelegatesStringDatesToFilter(): void
    {
        $product = $this->createProduct('2026-06-01 00:00:00', '2026-06-30 23:59:59');

        $this->assertTrue((new NewLabel($this->createFilter()))->isNew($product));
    }

    public function testIsNewPassesNullWhenDatesAreNotStrings(): void
    {
        $product = $this->createProduct(null, null);

        $this->assertFalse((new NewLabel($this->createFilter()))->isNew($product));
    }

    private function createFilter(): NewsDateFilter
    {
        $localeDate = $this->createStub(TimezoneInterface::class);
        $localeDate->method('date')->willReturn(new DateTime('2026-06-15 12:00:00'));

        return new NewsDateFilter($localeDate);
    }

    private function createProduct(?string $from, ?string $to): Product
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $product->setData('news_from_date', $from);
        $product->setData('news_to_date', $to);

        return $product;
    }
}
