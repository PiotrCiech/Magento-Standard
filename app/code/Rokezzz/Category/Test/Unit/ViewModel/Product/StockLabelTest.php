<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\ViewModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Config;
use Rokezzz\Category\ViewModel\Product\StockLabel;

final class StockLabelTest extends TestCase
{
    public function testReturnsNullWhenFeatureDisabled(): void
    {
        $viewModel = $this->createViewModel(false, 5, $this->createStockItem(true, 2.0));

        $this->assertNull($viewModel->getLabel($this->createProduct(7)));
    }

    public function testReturnsNullWhenStockNotManaged(): void
    {
        $viewModel = $this->createViewModel(true, 5, $this->createStockItem(false, 2.0));

        $this->assertNull($viewModel->getLabel($this->createProduct(7)));
    }

    public function testReturnsNullWhenOutOfStock(): void
    {
        $viewModel = $this->createViewModel(true, 5, $this->createStockItem(true, 0.0));

        $this->assertNull($viewModel->getLabel($this->createProduct(7)));
    }

    public function testReturnsNullWhenAboveThreshold(): void
    {
        $viewModel = $this->createViewModel(true, 5, $this->createStockItem(true, 6.0));

        $this->assertNull($viewModel->getLabel($this->createProduct(7)));
    }

    public function testReturnsLabelWhenAtOrBelowThreshold(): void
    {
        $viewModel = $this->createViewModel(true, 5, $this->createStockItem(true, 3.0));

        $this->assertSame('Last 3 in stock', $viewModel->getLabel($this->createProduct(7)));
    }

    public function testReturnsSingularLabelForLastItem(): void
    {
        $viewModel = $this->createViewModel(true, 5, $this->createStockItem(true, 1.0));

        $this->assertSame('Last item in stock', $viewModel->getLabel($this->createProduct(7)));
    }

    private function createViewModel(bool $enabled, int $threshold, StockItemInterface $stockItem): StockLabel
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn($enabled);
        $scopeConfig->method('getValue')->willReturn((string) $threshold);

        $stockRegistry = $this->createStub(StockRegistryInterface::class);
        $stockRegistry->method('getStockItem')->willReturn($stockItem);

        return new StockLabel(new Config($scopeConfig), $stockRegistry);
    }

    private function createStockItem(bool $manageStock, float $qty): StockItemInterface
    {
        $stockItem = $this->createStub(StockItemInterface::class);
        $stockItem->method('getManageStock')->willReturn($manageStock);
        $stockItem->method('getQty')->willReturn($qty);

        return $stockItem;
    }

    private function createProduct(int $id): ProductInterface
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getId')->willReturn($id);

        return $product;
    }
}
