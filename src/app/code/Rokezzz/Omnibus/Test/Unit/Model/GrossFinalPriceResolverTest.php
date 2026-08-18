<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Api\AttributeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config as TaxConfig;
use PHPUnit\Framework\TestCase;
use Rokezzz\Omnibus\Model\GrossFinalPriceResolver;

final class GrossFinalPriceResolverTest extends TestCase
{
    public function testResolveRegularReturnsPriceWhenTaxIncluded(): void
    {
        $product = $this->createStub(Product::class);
        $product->method('getPrice')->willReturn(100.0);

        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(1);

        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $taxConfig = $this->createMock(TaxConfig::class);
        $taxConfig->method('priceIncludesTax')->with(1)->willReturn(true);

        $resolver = new GrossFinalPriceResolver(
            $this->createStub(TaxCalculationInterface::class),
            $taxConfig,
            $storeManager
        );

        $this->assertSame(100.0, $resolver->resolveRegular($product, 1));
    }

    public function testResolveRegularAddsTaxWhenPricesExcludeTax(): void
    {
        $taxAttribute = $this->createStub(AttributeInterface::class);
        $taxAttribute->method('getValue')->willReturn(2);

        $product = $this->createMock(Product::class);
        $product->method('getPrice')->willReturn(100.0);
        $product->method('getCustomAttribute')
            ->with('tax_class_id')
            ->willReturn($taxAttribute);

        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(1);

        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $taxConfig = $this->createMock(TaxConfig::class);
        $taxConfig->method('priceIncludesTax')->with(1)->willReturn(false);

        $taxCalculation = $this->createMock(TaxCalculationInterface::class);
        $taxCalculation->method('getCalculatedRate')
            ->with(2, null, 1)
            ->willReturn(23.0);

        $resolver = new GrossFinalPriceResolver($taxCalculation, $taxConfig, $storeManager);

        $this->assertSame(123.0, $resolver->resolveRegular($product, 1));
    }
}
