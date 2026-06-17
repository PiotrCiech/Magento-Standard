<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Omnibus\Api\GrossFinalPriceResolverInterface;
use Rokezzz\Omnibus\Model\Config;
use Rokezzz\Omnibus\Model\PriceHistoryRecorder;
use Rokezzz\Omnibus\Model\ResourceModel\PriceHistory;
use Rokezzz\Omnibus\Model\SimpleProductEligibility;

class PriceHistoryRecorderTest extends TestCase
{
    public function testRecordForAllStoresSkipsIneligibleProduct(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getTypeId')->willReturn('configurable');

        $priceHistory = $this->createMock(PriceHistory::class);
        $priceHistory->expects($this->never())->method('insert');

        $recorder = new PriceHistoryRecorder(
            $this->createStub(Config::class),
            $this->createStub(GrossFinalPriceResolverInterface::class),
            $priceHistory,
            $this->createStub(ProductRepositoryInterface::class),
            new SimpleProductEligibility(),
            $this->createStub(StoreManagerInterface::class)
        );

        $recorder->recordForAllStores($product);
    }

    public function testRecordForProductSkipsWhenFeatureDisabled(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(false);

        $priceHistory = $this->createMock(PriceHistory::class);
        $priceHistory->expects($this->never())->method('insert');

        $recorder = new PriceHistoryRecorder(
            $config,
            $this->createStub(GrossFinalPriceResolverInterface::class),
            $priceHistory,
            $this->createStub(ProductRepositoryInterface::class),
            new SimpleProductEligibility(),
            $this->createStub(StoreManagerInterface::class)
        );

        $recorder->recordForProduct(42, 1);
    }

    public function testRecordForProductInsertsResolvedPrice(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getId')->willReturn(42);
        $product->method('getTypeId')->willReturn(Type::TYPE_SIMPLE);

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(true);

        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->method('getById')->with(42, false, 1)->willReturn($product);

        $priceResolver = $this->createMock(GrossFinalPriceResolverInterface::class);
        $priceResolver->method('resolve')->with($product, 1)->willReturn(99.99);

        $priceHistory = $this->createMock(PriceHistory::class);
        $priceHistory->expects($this->once())->method('insert')->with(42, 1, 99.99);

        $recorder = new PriceHistoryRecorder(
            $config,
            $priceResolver,
            $priceHistory,
            $productRepository,
            new SimpleProductEligibility(),
            $this->createStub(StoreManagerInterface::class)
        );

        $recorder->recordForProduct(42, 1);
    }
}
