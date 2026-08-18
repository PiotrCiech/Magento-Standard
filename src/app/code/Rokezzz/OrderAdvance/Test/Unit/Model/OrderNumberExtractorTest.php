<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Test\Unit\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\OrderAdvance\Model\OrderNumberExtractor;

final class OrderNumberExtractorTest extends TestCase
{
    public function testFromPaymentReturnsNumberWhenExtensionAttributeIsPresent(): void
    {
        $extensionAttributes = new class {
            public function getCustomerOrderNumber(): string
            {
                return 'PO-123';
            }
        };

        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getExtensionAttributes')->willReturn($extensionAttributes);

        $this->assertSame('PO-123', (new OrderNumberExtractor())->fromPayment($payment));
    }

    public function testFromShippingInformationReturnsNullWhenExtensionAttributesAreMissing(): void
    {
        $addressInformation = $this->createMock(ShippingInformationInterface::class);
        $addressInformation->method('getExtensionAttributes')->willReturn(null);

        $this->assertNull((new OrderNumberExtractor())->fromShippingInformation($addressInformation));
    }
}
