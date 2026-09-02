<?php

declare(strict_types=1);

namespace Rokezzz\Category\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Category\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testIsNewEnabledReadsFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);

        $this->assertTrue((new Config($this->scopeConfig))->isNewEnabled(1));
    }

    public function testIsBestsellersEnabledReadsFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(false);

        $this->assertFalse((new Config($this->scopeConfig))->isBestsellersEnabled(1));
    }

    public function testBadgeFlagsReadFromScopeConfig(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);

        $config = new Config($this->scopeConfig);

        $this->assertTrue($config->isBestsellerBadgeEnabled(1));
        $this->assertTrue($config->isDiscountBadgeEnabled(1));
        $this->assertTrue($config->isLowStockBadgeEnabled(1));
    }

    public function testGetLowStockThresholdFallsBackToDefaultWhenInvalid(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('0');

        $this->assertSame(5, (new Config($this->scopeConfig))->getLowStockThreshold(1));
    }

    public function testGetLowStockThresholdReturnsConfiguredValue(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('3');

        $this->assertSame(3, (new Config($this->scopeConfig))->getLowStockThreshold(1));
    }
}
