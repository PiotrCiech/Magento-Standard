<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Reorder as ReorderHelper;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Rokezzz\ResubmittingArchivedOrders\Model\ArchivedOrderResubmitValidator;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

final class ArchivedOrderResubmitValidatorTest extends TestCase
{
    public function testGetOrderForResubmitReturnsOrderWhenValid(): void
    {
        $order = $this->createOrderStub(customerId: 10, storeId: 1, isArchived: true);

        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);

        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('get')->with(100)->willReturn($order);

        $reorderHelper = $this->createMock(ReorderHelper::class);
        $reorderHelper->method('isAllowed')->with(1)->willReturn(true);

        $validator = new ArchivedOrderResubmitValidator($config, $orderRepository, $reorderHelper);

        $this->assertSame($order, $validator->getOrderForResubmit(10, 100));
    }

    public function testGetOrderForResubmitThrowsWhenFeatureDisabled(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(false);

        $validator = new ArchivedOrderResubmitValidator(
            $config,
            $this->createStub(OrderRepositoryInterface::class),
            $this->createStub(ReorderHelper::class)
        );

        $this->expectException(LocalizedException::class);

        $validator->getOrderForResubmit(10, 100);
    }

    public function testGetOrderForResubmitThrowsWhenCustomerDoesNotOwnOrder(): void
    {
        $order = $this->createOrderStub(customerId: 99, storeId: 1, isArchived: true);

        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);

        $orderRepository = $this->createStub(OrderRepositoryInterface::class);
        $orderRepository->method('get')->willReturn($order);

        $validator = new ArchivedOrderResubmitValidator(
            $config,
            $orderRepository,
            $this->createStub(ReorderHelper::class)
        );

        $this->expectException(LocalizedException::class);

        $validator->getOrderForResubmit(10, 100);
    }

    private function createOrderStub(int $customerId, int $storeId, bool $isArchived): OrderInterface
    {
        $order = $this->createMock(Order::class);
        $order->method('getCustomerId')->willReturn($customerId);
        $order->method('getStoreId')->willReturn($storeId);
        $order->method('getData')->with('rokezzz_is_archived')->willReturn($isArchived ? 1 : 0);

        return $order;
    }
}
