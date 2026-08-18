<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\InPost\Model\Config;

final class ConfigTest extends TestCase
{
    public function testIsEnabledReadsFlag(): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn(false);

        $this->assertFalse((new Config($scopeConfig))->isEnabled(1));
    }
}
