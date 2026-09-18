<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Test\Unit\Model\Config\Source;

use Magento\Tax\Model\Config as TaxConfig;
use PHPUnit\Framework\TestCase;
use Rokezzz\Tax\Model\Config\Source\PriceDisplayMode;

final class PriceDisplayModeTest extends TestCase
{
    public function testToOptionArrayReturnsIncludingAndExcludingTax(): void
    {
        $options = (new PriceDisplayMode())->toOptionArray();

        $this->assertSame(
            [
                TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX,
            ],
            array_column($options, 'value')
        );
    }
}
