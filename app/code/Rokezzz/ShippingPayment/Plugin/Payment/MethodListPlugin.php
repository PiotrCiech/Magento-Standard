<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Plugin\Payment;

use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\Data\CartInterface;
use Rokezzz\ShippingPayment\Model\PaymentAvailability;

final class MethodListPlugin
{
    public function __construct(
        private readonly PaymentAvailability $paymentAvailability
    ) {
    }

    /**
     * @param mixed[] $result
     * @return mixed[]
     */
    public function afterGetAvailableMethods(
        MethodList $subject,
        array $result,
        ?CartInterface $quote = null
    ): array {
        $filtered = [];

        foreach ($result as $method) {
            if ($this->paymentAvailability->isPaymentAllowed($method->getCode(), $quote)) {
                $filtered[] = $method;
            }
        }

        return $filtered;
    }
}
