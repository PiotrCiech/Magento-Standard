<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\OrderCommentExtractor;

final class OrderCommentExtractorTest extends TestCase
{
    public function testFromPaymentReturnsCommentWhenExtensionAttributeIsPresent(): void
    {
        $extensionAttributes = new class {
            public function getOrderComment(): string
            {
                return 'Leave at reception';
            }
        };

        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getExtensionAttributes')->willReturn($extensionAttributes);

        $comment = (new OrderCommentExtractor())->fromPayment($payment);

        $this->assertSame('Leave at reception', $comment);
    }

    public function testFromShippingInformationReturnsNullWhenExtensionAttributesAreMissing(): void
    {
        $addressInformation = $this->createMock(ShippingInformationInterface::class);
        $addressInformation->method('getExtensionAttributes')->willReturn(null);

        $comment = (new OrderCommentExtractor())->fromShippingInformation($addressInformation);

        $this->assertNull($comment);
    }
}
