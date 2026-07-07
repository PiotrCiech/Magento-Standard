<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\ContractWithdrawal\Model\Config;
use Rokezzz\ContractWithdrawal\Model\WithdrawalEligibilityChecker;
use Rokezzz\ContractWithdrawal\Model\WithdrawalRequestProvider;

final class WithdrawalEligibilityCheckerTest extends TestCase
{
    private Config&MockObject $config;
    private WithdrawalRequestProvider&MockObject $withdrawalRequestProvider;
    private DateTime&MockObject $dateTime;
    private WithdrawalEligibilityChecker $withdrawalEligibilityChecker;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->withdrawalRequestProvider = $this->createMock(WithdrawalRequestProvider::class);
        $this->dateTime = $this->createMock(DateTime::class);
        $this->withdrawalEligibilityChecker = new WithdrawalEligibilityChecker(
            $this->config,
            $this->withdrawalRequestProvider,
            $this->dateTime
        );
    }

    public function testAssertCanSubmitThrowsWhenRequestAlreadyExists(): void
    {
        $order = $this->createOrderMock();

        $this->config->method('isEnabled')->willReturn(true);
        $this->withdrawalRequestProvider->method('getByOrderId')->willReturn($this->createStub(\Rokezzz\ContractWithdrawal\Model\WithdrawalRequest::class));

        $this->expectException(LocalizedException::class);

        $this->withdrawalEligibilityChecker->assertCanSubmit($order, 12);
    }

    public function testAssertCanSubmitThrowsWhenWithdrawalPeriodExpired(): void
    {
        $order = $this->createOrderMock('2026-06-01 09:00:00');

        $this->config->method('isEnabled')->willReturn(true);
        $this->config->method('getWithdrawalPeriodDays')->willReturn(14);
        $this->withdrawalRequestProvider->method('getByOrderId')->willReturn(null);
        $this->dateTime->method('gmtDate')->willReturn('2026-06-20 12:00:00');

        $this->expectException(LocalizedException::class);

        $this->withdrawalEligibilityChecker->assertCanSubmit($order, 12);
    }

    public function testAssertCanSubmitSucceedsWithinWithdrawalPeriod(): void
    {
        $order = $this->createOrderMock('2026-07-01 09:00:00');

        $this->config->method('isEnabled')->willReturn(true);
        $this->config->method('getWithdrawalPeriodDays')->willReturn(14);
        $this->withdrawalRequestProvider->method('getByOrderId')->willReturn(null);
        $this->dateTime->method('gmtDate')->willReturn('2026-07-10 12:00:00');

        $this->withdrawalEligibilityChecker->assertCanSubmit($order, 12);

        $this->assertTrue(true);
    }

    private function createOrderMock(string $createdAt = '2026-07-01 09:00:00'): OrderInterface&MockObject
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getStoreId')->willReturn(1);
        $order->method('getCustomerId')->willReturn(12);
        $order->method('getState')->willReturn(Order::STATE_PROCESSING);
        $order->method('getEntityId')->willReturn(34);
        $order->method('getCreatedAt')->willReturn($createdAt);

        return $order;
    }
}
