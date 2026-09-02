<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Test\Unit\Plugin\Catalog\Layer;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\FilterList;
use PHPUnit\Framework\TestCase;
use Rokezzz\CatalogListing\Plugin\Catalog\Layer\FilterListUniqueFiltersPlugin;

final class FilterListUniqueFiltersPluginTest extends TestCase
{
    public function testAfterGetFiltersKeepsFirstFilterPerRequestVar(): void
    {
        $priceOne = $this->createStub(FilterInterface::class);
        $priceOne->method('getRequestVar')->willReturn('price');

        $priceTwo = $this->createStub(FilterInterface::class);
        $priceTwo->method('getRequestVar')->willReturn('price');

        $color = $this->createStub(FilterInterface::class);
        $color->method('getRequestVar')->willReturn('color');

        $plugin = new FilterListUniqueFiltersPlugin();

        $this->assertSame(
            [$priceOne, $color],
            $plugin->afterGetFilters(
                $this->createStub(FilterList::class),
                [$priceOne, $priceTwo, $color]
            )
        );
    }
}
