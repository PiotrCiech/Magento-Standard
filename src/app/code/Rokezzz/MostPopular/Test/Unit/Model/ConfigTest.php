<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\MostPopular\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testGetProductsCountFallsBackToDefault(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(0);

        $this->assertSame(8, (new Config($this->scopeConfig))->getProductsCount(1));
    }

    public function testGetPeriodFallsBackToMonthForUnknownValue(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('week');

        $this->assertSame('month', (new Config($this->scopeConfig))->getPeriod(1));
    }
}
