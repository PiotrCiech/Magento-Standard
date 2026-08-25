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
}
