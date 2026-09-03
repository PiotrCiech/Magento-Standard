<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Test\Unit\Plugin\Elasticsearch;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\PriceFieldsProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config as TaxConfig;
use PHPUnit\Framework\TestCase;
use Rokezzz\Tax\Model\Search\DisplayPriceConverter;
use Rokezzz\Tax\Model\Search\ProductTaxClassIdResolver;
use Rokezzz\Tax\Plugin\Elasticsearch\PriceFieldsProviderPlugin;

final class PriceFieldsProviderPluginTest extends TestCase
{
    public function testAfterGetFieldsReturnsOriginalDataWhenConversionIsNotNeeded(): void
    {
        $taxConfig = $this->createMock(TaxConfig::class);
        $taxConfig->method('needPriceConversion')->willReturn(false);

        $plugin = new PriceFieldsProviderPlugin(
            new DisplayPriceConverter(
                $taxConfig,
                $this->createStub(TaxCalculationInterface::class),
                $this->createStub(ScopeConfigInterface::class)
            ),
            $this->createStub(ProductTaxClassIdResolver::class)
        );

        $fields = [10 => ['price_0_1' => '88.000000']];

        $this->assertSame(
            $fields,
            $plugin->afterGetFields(
                $this->createStub(PriceFieldsProvider::class),
                $fields,
                [10],
                1
            )
        );
    }

    public function testAfterGetFieldsConvertsIndexedPricesToDisplayPrices(): void
    {
        $taxConfig = $this->createMock(TaxConfig::class);
        $taxConfig->method('needPriceConversion')->willReturn(TaxConfig::PRICE_CONVERSION_MINUS);
        $taxConfig->method('priceIncludesTax')->willReturn(true);
        $taxConfig->method('getPriceDisplayType')->willReturn(TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX);

        $taxCalculation = $this->createMock(TaxCalculationInterface::class);
        $taxCalculation->method('getCalculatedRate')->with(2, null, 1)->willReturn(23.0);

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(2);

        $resolver = $this->createMock(ProductTaxClassIdResolver::class);
        $resolver->method('resolve')->with([10], 1)->willReturn([10 => 2]);

        $plugin = new PriceFieldsProviderPlugin(
            new DisplayPriceConverter($taxConfig, $taxCalculation, $scopeConfig),
            $resolver
        );

        $result = $plugin->afterGetFields(
            $this->createStub(PriceFieldsProvider::class),
            [10 => ['price_0_1' => '88.000000']],
            [10],
            1
        );

        $this->assertSame(sprintf('%F', 71.54), $result[10]['price_0_1']);
    }
}
