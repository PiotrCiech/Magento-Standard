<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Wishlist\Model\Config;

final class ConfigTest extends TestCase
{
    public function testIsSharingEnabledReadsFlag(): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn(false);

        $this->assertFalse((new Config($scopeConfig))->isSharingEnabled(1));
    }
}
