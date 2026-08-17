<?php

declare(strict_types=1);

namespace Rokezzz\ChatBot\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\ChatBot\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testIsActiveRequiresEnabledFlagAndPropertyId(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);
        $this->scopeConfig->method('getValue')->willReturn('abc123');

        $this->assertTrue((new Config($this->scopeConfig))->isActive(1));
    }

    public function testGetPropertyIdRejectsInvalidCharacters(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);
        $this->scopeConfig->method('getValue')->willReturn('abc-123');

        $this->assertFalse((new Config($this->scopeConfig))->isActive(1));
    }

    public function testGetWidgetIdFallsBackToDefault(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('');

        $this->assertSame('default', (new Config($this->scopeConfig))->getWidgetId(1));
    }

    public function testGetZIndexReturnsNullForNonNumericValue(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('auto');

        $this->assertNull((new Config($this->scopeConfig))->getZIndex(1));
    }
}
