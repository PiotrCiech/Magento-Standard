<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\OrderAdvance\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testIsOrderNumberEnabledReadsFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);

        $this->assertTrue((new Config($this->scopeConfig))->isOrderNumberEnabled(1));
    }

    public function testGetOrderNumberMaxLengthFallsBackToDefaultWhenInvalid(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('0');

        $this->assertSame(64, (new Config($this->scopeConfig))->getOrderNumberMaxLength(1));
    }

    public function testGetStatusBarVisibleDaysFallsBackToDefaultWhenInvalid(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(-1);

        $this->assertSame(14, (new Config($this->scopeConfig))->getStatusBarVisibleDays(1));
    }
}
