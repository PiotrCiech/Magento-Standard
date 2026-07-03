<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Test\Unit\Model;

use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\PointsCalculator;

final class PointsCalculatorTest extends TestCase
{
    private Config&MockObject $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('isEnabled')->willReturn(true);
        $this->config->method('getPointsPerCurrency')->willReturn(1.0);
    }

    public function testCalculateEarnedPointsUsesGrandTotalWithoutShipping(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getStoreId')->willReturn(1);
        $order->method('getGrandTotal')->willReturn(120.0);
        $order->method('getShippingInclTax')->willReturn(20.0);

        $calculator = new PointsCalculator($this->config);

        $this->assertSame(100, $calculator->calculateEarnedPoints($order));
    }

    public function testCalculateEarnedPointsReturnsZeroWhenDisabled(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->willReturn(false);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getStoreId')->willReturn(1);

        $calculator = new PointsCalculator($config);

        $this->assertSame(0, $calculator->calculateEarnedPoints($order));
    }
}
