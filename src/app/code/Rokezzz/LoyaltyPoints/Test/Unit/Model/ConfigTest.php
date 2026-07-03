<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\LoyaltyPoints\Model\Config;

final class ConfigTest extends TestCase
{
    private ScopeConfigInterface&MockObject $scopeConfig;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
    }

    public function testIsEnabledReadsFlag(): void
    {
        $this->scopeConfig->method('isSetFlag')->willReturn(true);

        $this->assertTrue((new Config($this->scopeConfig))->isEnabled(1));
    }

    public function testGetMinPointsToRedeemFallsBackToDefault(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(0);

        $this->assertSame(100, (new Config($this->scopeConfig))->getMinPointsToRedeem(1));
    }

    public function testGetMaxRedemptionPercentIsCappedAtOneHundred(): void
    {
        $this->scopeConfig->method('getValue')->willReturn(150.0);

        $this->assertSame(100.0, (new Config($this->scopeConfig))->getMaxRedemptionPercent(1));
    }
}
