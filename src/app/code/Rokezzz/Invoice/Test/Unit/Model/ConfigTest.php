<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&Stub $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createStub(ScopeConfigInterface::class);
    }

    public function testIsKsefEnabledRequiresModuleFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturnMap([
            ['rokezzz_invoice/general/enabled', 'store', 1, true],
            ['rokezzz_invoice/ksef/enabled', 'store', 1, true],
        ]);

        $this->assertTrue((new Config($this->scopeConfig))->isKsefEnabled(1));
    }

    public function testIsKsefEnabledReturnsFalseWhenModuleDisabled(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(false);

        $this->assertFalse((new Config($this->scopeConfig))->isKsefEnabled(1));
    }

    public function testGetKsefStartDateFallsBackToDefaultWhenEmpty(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('  ');

        $this->assertSame('2026-01-01', (new Config($this->scopeConfig))->getKsefStartDate(1));
    }

    public function testGetInvoicesDirectoryTrimsSlashes(): void
    {
        $this->scopeConfig->method('getValue')->willReturn('/custom/invoices/');

        $this->assertSame('custom/invoices', (new Config($this->scopeConfig))->getInvoicesDirectory(1));
    }
}
