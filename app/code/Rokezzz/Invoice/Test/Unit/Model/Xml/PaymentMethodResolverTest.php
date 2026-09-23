<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model\Xml;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Invoice\PaymentMethod;
use Rokezzz\Invoice\Model\Xml\PaymentMethodResolver;

final class PaymentMethodResolverTest extends TestCase
{
    public function testResolveReturnsBankTransferWhenOrderIsMissing(): void
    {
        $invoice = $this->invoiceWithOrder(null);

        $this->assertSame(PaymentMethod::BankTransfer, (new PaymentMethodResolver())->resolve($invoice));
    }

    public function testResolveMapsCashOnDeliveryToCash(): void
    {
        $invoice = $this->invoiceWithOrder($this->createOrder('cashondelivery'));

        $this->assertSame(PaymentMethod::Cash, (new PaymentMethodResolver())->resolve($invoice));
    }

    public function testResolveMapsFreeMethodToPaidInAdvance(): void
    {
        $invoice = $this->invoiceWithOrder($this->createOrder('free'));

        $this->assertSame(PaymentMethod::PaidInAdvance, (new PaymentMethodResolver())->resolve($invoice));
    }

    private function createOrder(string $method): OrderInterface
    {
        $payment = $this->createStub(OrderPaymentInterface::class);
        $payment->method('getMethod')->willReturn($method);

        $order = $this->createStub(OrderInterface::class);
        $order->method('getPayment')->willReturn($payment);

        return $order;
    }

    private function invoiceWithOrder(?OrderInterface $order): Invoice
    {
        $invoice = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOrder'])
            ->getMock();
        $invoice->method('getOrder')->willReturn($order);

        return $invoice;
    }
}
