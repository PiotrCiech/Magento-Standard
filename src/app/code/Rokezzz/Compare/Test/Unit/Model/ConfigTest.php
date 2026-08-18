<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Compare\Model\Config;

final class ConfigTest extends TestCase
{
    public function testIsEnabledReadsFlag(): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn(true);

        $this->assertTrue((new Config($scopeConfig))->isEnabled(1));
    }
}
