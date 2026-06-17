<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Test\Unit\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rokezzz\OrderOwnerNotification\Model\Config;
use Rokezzz\OrderOwnerNotification\Model\OrderOwnerNotificationContextResolver;

class OrderOwnerNotificationContextResolverTest extends TestCase
{
    public function testResolveReturnsNullWhenFeatureDisabled(): void
    {
        $order = $this->createOrderStub(storeId: 1);

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(false);

        $resolver = new OrderOwnerNotificationContextResolver(
            $config,
            $this->createStub(LoggerInterface::class)
        );

        $this->assertNull($resolver->resolve($order));
    }

    public function testResolveReturnsNullWhenRecipientEmailIsInvalid(): void
    {
        $order = $this->createOrderStub(storeId: 1, entityId: 100);

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(true);
        $config->method('getRecipientEmail')->with(1)->willReturn('not-an-email');

        $resolver = new OrderOwnerNotificationContextResolver(
            $config,
            $this->createStub(LoggerInterface::class)
        );

        $this->assertNull($resolver->resolve($order));
    }

    public function testResolveReturnsContextForValidOrder(): void
    {
        $order = $this->createStub(Order::class);
        $order->method('getStoreId')->willReturn(1);
        $order->method('getEntityId')->willReturn(100);

        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->with(1)->willReturn(true);
        $config->method('getRecipientEmail')->with(1)->willReturn('owner@example.com');

        $context = (new OrderOwnerNotificationContextResolver(
            $config,
            $this->createStub(LoggerInterface::class)
        ))->resolve($order);

        $this->assertNotNull($context);
        $this->assertSame($order, $context->order);
        $this->assertSame(1, $context->storeId);
        $this->assertSame('owner@example.com', $context->recipientEmail);
    }

    private function createOrderStub(int $storeId, int $entityId = 1): OrderInterface
    {
        $order = $this->createStub(OrderInterface::class);
        $order->method('getStoreId')->willReturn($storeId);
        $order->method('getEntityId')->willReturn($entityId);

        return $order;
    }
}
