<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Test\Unit\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\OrderAdvance\Model\OrderNumberExtractor;
use Rokezzz\OrderAdvance\Model\OrderNumberRequestReader;

final class OrderNumberRequestReaderTest extends TestCase
{
    private HttpRequest&MockObject $request;
    private RestRequest&MockObject $restRequest;

    protected function setUp(): void
    {
        $this->request = $this->createMock(HttpRequest::class);
        $this->restRequest = $this->createMock(RestRequest::class);
    }

    public function testFromShippingInformationReadsRestBodyParams(): void
    {
        $addressInformation = $this->createMock(ShippingInformationInterface::class);
        $addressInformation->method('getExtensionAttributes')->willReturn(null);
        $this->restRequest->method('getBodyParams')->willReturn([
            'addressInformation' => [
                'extension_attributes' => [
                    'customer_order_number' => '  PO-55  ',
                ],
            ],
        ]);
        $this->request->method('getContent')->willReturn('');

        $reader = new OrderNumberRequestReader(
            $this->request,
            $this->restRequest,
            new OrderNumberExtractor()
        );

        $this->assertSame('PO-55', $reader->fromShippingInformation($addressInformation));
    }

    public function testFromCurrentRequestReadsPaymentPayloadFirst(): void
    {
        $this->restRequest->method('getBodyParams')->willReturn([
            'paymentMethod' => [
                'extension_attributes' => [
                    'customer_order_number' => 'PAY-1',
                ],
            ],
            'addressInformation' => [
                'extension_attributes' => [
                    'customer_order_number' => 'SHIP-1',
                ],
            ],
        ]);
        $this->request->method('getContent')->willReturn('');

        $reader = new OrderNumberRequestReader(
            $this->request,
            $this->restRequest,
            new OrderNumberExtractor()
        );

        $this->assertSame('PAY-1', $reader->fromCurrentRequest());
    }
}
