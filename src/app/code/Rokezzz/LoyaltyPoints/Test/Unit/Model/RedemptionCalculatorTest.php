<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

final class RedemptionCalculatorTest extends TestCase
{
    private Config&MockObject $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('getPointValue')->willReturn(0.1);
        $this->config->method('getMinPointsToRedeem')->willReturn(100);
        $this->config->method('getMaxRedemptionPercent')->willReturn(50.0);
    }

    public function testResolveAppliedPointsReturnsZeroBelowMinimum(): void
    {
        $calculator = new RedemptionCalculator($this->config);

        $this->assertSame(0, $calculator->resolveAppliedPoints(50, 500, 200.0, 1));
    }

    public function testResolveAppliedPointsCapsByBalanceAndPercent(): void
    {
        $calculator = new RedemptionCalculator($this->config);

        $this->assertSame(500, $calculator->resolveAppliedPoints(800, 500, 200.0, 1));
    }

    public function testGetMonetaryDiscountUsesPointValue(): void
    {
        $calculator = new RedemptionCalculator($this->config);

        $this->assertSame(10.0, $calculator->getMonetaryDiscount(100, 1));
    }
}
