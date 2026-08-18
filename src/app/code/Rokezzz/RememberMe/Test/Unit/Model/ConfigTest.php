<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\RememberMe\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testGetCustomerLifetimeWithRememberReadsConfiguredValue(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(3600);

        $this->assertSame(3600, (new Config($this->scopeConfig))->getCustomerLifetimeWithRemember(1));
    }

    public function testGetCustomerLifetimeWithoutRememberFallsBackToDefaultWhenInvalid(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(0);

        $this->assertSame(1800, (new Config($this->scopeConfig))->getCustomerLifetimeWithoutRemember(1));
    }
}
