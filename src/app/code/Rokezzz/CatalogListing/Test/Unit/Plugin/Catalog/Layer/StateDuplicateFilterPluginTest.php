<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Test\Unit\Plugin\Catalog\Layer;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\State;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use PHPUnit\Framework\TestCase;
use Rokezzz\CatalogListing\Plugin\Catalog\Layer\StateDuplicateFilterPlugin;

final class StateDuplicateFilterPluginTest extends TestCase
{
    public function testAroundAddFilterSkipsDuplicateRequestVarAndValue(): void
    {
        $priceFilter = $this->createStub(FilterInterface::class);
        $priceFilter->method('getRequestVar')->willReturn('price');

        $existing = $this->createItem($priceFilter, [80, 90]);
        $duplicate = $this->createItem($priceFilter, [80, 90]);

        $state = $this->createMock(State::class);
        $state->method('getFilters')->willReturn([$existing]);

        $called = false;
        $plugin = new StateDuplicateFilterPlugin();
        $result = $plugin->aroundAddFilter(
            $state,
            static function () use (&$called, $state) {
                $called = true;

                return $state;
            },
            $duplicate
        );

        $this->assertFalse($called);
        $this->assertSame($state, $result);
    }

    public function testAroundAddFilterAllowsDifferentValue(): void
    {
        $priceFilter = $this->createStub(FilterInterface::class);
        $priceFilter->method('getRequestVar')->willReturn('price');

        $existing = $this->createItem($priceFilter, [80, 90]);
        $next = $this->createItem($priceFilter, [90, 100]);

        $state = $this->createMock(State::class);
        $state->method('getFilters')->willReturn([$existing]);

        $called = false;
        $plugin = new StateDuplicateFilterPlugin();
        $plugin->aroundAddFilter(
            $state,
            static function () use (&$called, $state) {
                $called = true;

                return $state;
            },
            $next
        );

        $this->assertTrue($called);
    }

    /**
     * @param int[] $value
     */
    private function createItem(FilterInterface $filter, array $value): Item
    {
        $item = new Item(
            $this->createStub(UrlInterface::class),
            $this->createStub(Pager::class)
        );
        $item->setFilter($filter);
        $item->setValue($value);

        return $item;
    }
}
