<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Test\Unit\Model;

use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\OrderOwnerNotification\Model\OrderOwnerNotificationContext;
use Rokezzz\OrderOwnerNotification\Model\OrderOwnerNotificationContextResolver;
use Rokezzz\OrderOwnerNotification\Model\OrderOwnerNotificationEmailSender;
use Rokezzz\OrderOwnerNotification\Model\OrderOwnerNotifier;

class OrderOwnerNotifierTest extends TestCase
{
    public function testNotifySkipsWhenContextCannotBeResolved(): void
    {
        $order = $this->createStub(OrderInterface::class);

        $contextResolver = $this->createMock(OrderOwnerNotificationContextResolver::class);
        $contextResolver->method('resolve')->with($order)->willReturn(null);

        $emailSender = $this->createMock(OrderOwnerNotificationEmailSender::class);
        $emailSender->expects($this->never())->method('send');

        (new OrderOwnerNotifier($contextResolver, $emailSender))->notify($order);
    }

    public function testNotifySendsEmailWhenContextIsResolved(): void
    {
        $order = $this->createStub(OrderInterface::class);
        $context = $this->createStub(OrderOwnerNotificationContext::class);

        $contextResolver = $this->createMock(OrderOwnerNotificationContextResolver::class);
        $contextResolver->method('resolve')->with($order)->willReturn($context);

        $emailSender = $this->createMock(OrderOwnerNotificationEmailSender::class);
        $emailSender->expects($this->once())->method('send')->with($context);

        (new OrderOwnerNotifier($contextResolver, $emailSender))->notify($order);
    }
}
