<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testIsEnabledReadsConfigFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);

        $config = new Config($this->scopeConfig);

        $this->assertTrue($config->isEnabled());
    }

    public function testGetMaxLengthFallsBackToDefaultWhenInvalid(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('0');

        $config = new Config($this->scopeConfig);

        $this->assertSame(1000, $config->getMaxLength());
    }

    public function testGetLabelFallsBackToDefaultWhenEmpty(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('');

        $config = new Config($this->scopeConfig);

        $this->assertSame('Order notes', $config->getLabel());
    }
}
