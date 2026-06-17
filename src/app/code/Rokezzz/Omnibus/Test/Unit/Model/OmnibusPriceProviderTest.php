<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Test\Unit\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Omnibus\Api\GrossFinalPriceResolverInterface;
use Rokezzz\Omnibus\Model\Config;
use Rokezzz\Omnibus\Model\OmnibusPriceProvider;
use Rokezzz\Omnibus\Model\ResourceModel\PriceHistory;
use Rokezzz\Omnibus\Model\SimpleProductEligibility;

class OmnibusPriceProviderTest extends TestCase
{
    public function testGetDisplayDataReturnsNullWhenFeatureDisabled(): void
    {
        $product = $this->createProductStub();

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(false);

        $provider = new OmnibusPriceProvider(
            $config,
            $this->createStub(GrossFinalPriceResolverInterface::class),
            $this->createStub(PriceHistory::class),
            new SimpleProductEligibility(),
            $this->createStoreManagerStub()
        );

        $this->assertNull($provider->getDisplayData($product, 1));
    }

    public function testGetDisplayDataReturnsNullWhenPriceIsNotPromotional(): void
    {
        $product = $this->createProductStub();

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(true);

        $priceResolver = $this->createStub(GrossFinalPriceResolverInterface::class);
        $priceResolver->method('resolve')->willReturn(100.0);
        $priceResolver->method('resolveRegular')->willReturn(100.0);

        $provider = new OmnibusPriceProvider(
            $config,
            $priceResolver,
            $this->createStub(PriceHistory::class),
            new SimpleProductEligibility(),
            $this->createStoreManagerStub()
        );

        $this->assertNull($provider->getDisplayData($product, 1));
    }

    public function testGetDisplayDataReturnsDisplayDataForPromotionalPrice(): void
    {
        $product = $this->createProductStub();

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(true);
        $config->method('getPeriodDays')->with(1)->willReturn(30);

        $priceResolver = $this->createStub(GrossFinalPriceResolverInterface::class);
        $priceResolver->method('resolve')->willReturn(80.0);
        $priceResolver->method('resolveRegular')->willReturn(100.0);

        $priceHistory = $this->createMock(PriceHistory::class);
        $priceHistory->method('getMinimumPriceAbove')
            ->with(42, 1, 30, 80.0)
            ->willReturn(95.0);

        $displayData = (new OmnibusPriceProvider(
            $config,
            $priceResolver,
            $priceHistory,
            new SimpleProductEligibility(),
            $this->createStoreManagerStub()
        ))->getDisplayData($product, 1);

        $this->assertNotNull($displayData);
        $this->assertSame(95.0, $displayData->getLowestPrice());
        $this->assertSame(30, $displayData->getPeriodDays());
    }

    private function createProductStub(): ProductInterface
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getId')->willReturn(42);
        $product->method('getTypeId')->willReturn(Type::TYPE_SIMPLE);

        return $product;
    }

    private function createStoreManagerStub(): StoreManagerInterface
    {
        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(1);

        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        return $storeManager;
    }
}
