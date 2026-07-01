<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\OrderCommentExtractor;
use Rokezzz\Orders\Model\OrderCommentRequestReader;

final class OrderCommentRequestReaderTest extends TestCase
{
    private HttpRequest&MockObject $request;

    private RestRequest&MockObject $restRequest;

    private OrderCommentExtractor&MockObject $orderCommentExtractor;

    protected function setUp(): void
    {
        $this->request = $this->createMock(HttpRequest::class);
        $this->restRequest = $this->createMock(RestRequest::class);
        $this->orderCommentExtractor = $this->createMock(OrderCommentExtractor::class);
    }

    public function testFromShippingInformationReadsRestBodyParams(): void
    {
        $addressInformation = $this->createMock(ShippingInformationInterface::class);

        $this->orderCommentExtractor->method('fromShippingInformation')
            ->with($addressInformation)
            ->willReturn(null);
        $this->restRequest->method('getBodyParams')->willReturn([
            'addressInformation' => [
                'extension_attributes' => [
                    'order_comment' => '  Zostaw u drzwi  ',
                ],
            ],
        ]);
        $this->request->method('getContent')->willReturn('');

        $reader = new OrderCommentRequestReader($this->request, $this->restRequest, $this->orderCommentExtractor);

        $this->assertSame('Zostaw u drzwi', $reader->fromShippingInformation($addressInformation));
    }

    public function testFromCurrentRequestReadsPaymentPayloadFirst(): void
    {
        $this->restRequest->method('getBodyParams')->willReturn([
            'paymentMethod' => [
                'extension_attributes' => [
                    'order_comment' => 'Komentarz z płatności',
                ],
            ],
            'addressInformation' => [
                'extension_attributes' => [
                    'order_comment' => 'Komentarz z dostawy',
                ],
            ],
        ]);
        $this->request->method('getContent')->willReturn('');

        $reader = new OrderCommentRequestReader($this->request, $this->restRequest, $this->orderCommentExtractor);

        $this->assertSame('Komentarz z płatności', $reader->fromCurrentRequest());
    }
}
