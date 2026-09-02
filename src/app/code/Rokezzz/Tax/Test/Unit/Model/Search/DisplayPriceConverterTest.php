<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Test\Unit\Model\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config as TaxConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Tax\Model\Search\DisplayPriceConverter;

final class DisplayPriceConverterTest extends TestCase
{
    private TaxConfig&MockObject $taxConfig;
    private TaxCalculationInterface&MockObject $taxCalculation;
    private ScopeConfigInterface&MockObject $scopeConfig;
    private DisplayPriceConverter $converter;

    protected function setUp(): void
    {
        $this->taxConfig = $this->createMock(TaxConfig::class);
        $this->taxCalculation = $this->createMock(TaxCalculationInterface::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->converter = new DisplayPriceConverter(
            $this->taxConfig,
            $this->taxCalculation,
            $this->scopeConfig
        );
    }

    public function testConvertStripsTaxWhenCatalogIncludesAndDisplayExcludes(): void
    {
        $this->taxConfig->method('needPriceConversion')->with(1)->willReturn(TaxConfig::PRICE_CONVERSION_MINUS);
        $this->taxConfig->method('priceIncludesTax')->with(1)->willReturn(true);
        $this->taxConfig->method('getPriceDisplayType')->with(1)->willReturn(TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX);
        $this->taxCalculation->method('getCalculatedRate')->with(2, null, 1)->willReturn(23.0);

        $this->assertSame(71.54, $this->converter->convert(88.0, 2, 1));
    }

    public function testConvertAddsTaxWhenCatalogExcludesAndDisplayIncludes(): void
    {
        $this->taxConfig->method('needPriceConversion')->with(1)->willReturn(TaxConfig::PRICE_CONVERSION_PLUS);
        $this->taxConfig->method('priceIncludesTax')->with(1)->willReturn(false);
        $this->taxConfig->method('getPriceDisplayType')->with(1)->willReturn(TaxConfig::DISPLAY_TYPE_INCLUDING_TAX);
        $this->taxCalculation->method('getCalculatedRate')->with(2, null, 1)->willReturn(23.0);

        $this->assertSame(123.0, $this->converter->convert(100.0, 2, 1));
    }

    public function testConvertReturnsOriginalPriceWhenConversionIsNotNeeded(): void
    {
        $this->taxConfig->method('needPriceConversion')->with(1)->willReturn(false);

        $this->assertSame(88.0, $this->converter->convert(88.0, 2, 1));
    }

    public function testConvertReturnsOriginalPriceWhenTaxRateIsZero(): void
    {
        $this->taxConfig->method('needPriceConversion')->with(1)->willReturn(TaxConfig::PRICE_CONVERSION_MINUS);
        $this->taxCalculation->method('getCalculatedRate')->willReturn(0.0);

        $this->assertSame(88.0, $this->converter->convert(88.0, 2, 1));
    }
}
