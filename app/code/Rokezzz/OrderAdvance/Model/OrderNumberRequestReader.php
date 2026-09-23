<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Quote\Api\Data\PaymentInterface;

final class OrderNumberRequestReader
{
    /**
     * @var list<string>
     */
    private const SHIPPING_JSON_PATH = [
        'addressInformation',
        'extension_attributes',
        'customer_order_number',
    ];

    /**
     * @var list<string>
     */
    private const PAYMENT_JSON_PATH = [
        'paymentMethod',
        'extension_attributes',
        'customer_order_number',
    ];

    public function __construct(
        private readonly HttpRequest $request,
        private readonly RestRequest $restRequest,
        private readonly OrderNumberExtractor $orderNumberExtractor
    ) {
    }

    public function fromShippingInformation(ShippingInformationInterface $addressInformation): ?string
    {
        $orderNumber = $this->orderNumberExtractor->fromShippingInformation($addressInformation);

        if (!is_null($orderNumber)) {
            return $this->normalize($orderNumber);
        }

        return $this->fromJsonPath(self::SHIPPING_JSON_PATH);
    }

    public function fromPayment(PaymentInterface $payment): ?string
    {
        $orderNumber = $this->orderNumberExtractor->fromPayment($payment);

        if (!is_null($orderNumber)) {
            return $this->normalize($orderNumber);
        }

        return $this->fromJsonPath(self::PAYMENT_JSON_PATH);
    }

    public function fromCurrentRequest(): ?string
    {
        return $this->fromJsonPath(self::PAYMENT_JSON_PATH)
            ?? $this->fromJsonPath(self::SHIPPING_JSON_PATH);
    }

    /**
     * @param list<string> $path
     */
    private function fromJsonPath(array $path): ?string
    {
        $orderNumber = $this->readFromBodyParams($this->restRequest->getBodyParams(), $path);

        if (!is_null($orderNumber)) {
            return $orderNumber;
        }

        $content = $this->request->getContent();

        if ($content === '') {
            return null;
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            return null;
        }

        return $this->readFromBodyParams($data, $path);
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string> $path
     */
    private function readFromBodyParams(array $data, array $path): ?string
    {
        $node = $data;

        foreach ($path as $index => $segment) {
            if ($index === count($path) - 1) {
                if (!isset($node[$segment]) || !is_string($node[$segment])) {
                    return null;
                }

                return $this->normalize($node[$segment]);
            }

            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                return null;
            }

            $node = $node[$segment];
        }

        return null;
    }

    private function normalize(string $orderNumber): ?string
    {
        $orderNumber = trim($orderNumber);

        return $orderNumber === '' ? null : $orderNumber;
    }
}
