<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Test\Unit\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Reorder\Data\ReorderOutput;
use Magento\Sales\Model\Reorder\Reorder;
use PHPUnit\Framework\TestCase;
use Rokezzz\ResubmittingArchivedOrders\Model\ArchivedOrderResubmitValidator;
use Rokezzz\ResubmittingArchivedOrders\Model\ResubmitArchivedOrder;

final class ResubmitArchivedOrderTest extends TestCase
{
    public function testExecuteReordersArchivedOrderAndSetsQuoteInSession(): void
    {
        $order = $this->createStub(OrderInterface::class);
        $order->method('getIncrementId')->willReturn('100000001');
        $order->method('getStoreId')->willReturn(1);

        $cart = $this->createStub(CartInterface::class);
        $cart->method('getId')->willReturn(55);

        $output = $this->createStub(ReorderOutput::class);
        $output->method('getCart')->willReturn($cart);

        $validator = $this->createMock(ArchivedOrderResubmitValidator::class);
        $validator->method('getOrderForResubmit')->with(10, 100)->willReturn($order);

        $reorder = $this->createMock(Reorder::class);
        $reorder->method('execute')->with('100000001', '1')->willReturn($output);

        $checkoutSession = $this->createMock(CheckoutSession::class);
        $checkoutSession->expects($this->once())->method('setQuoteId')->with(55);

        $service = new ResubmitArchivedOrder($validator, $reorder, $checkoutSession);

        $this->assertSame($output, $service->execute(10, 100));
    }
}
