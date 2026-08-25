<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\CategoryAdvance\Model\Config;

final class ConfigTest extends TestCase
{
    public function testIsPdfCatalogEnabledReadsFlag(): void
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn(true);

        $this->assertTrue((new Config($scopeConfig))->isPdfCatalogEnabled(1));
    }
}
