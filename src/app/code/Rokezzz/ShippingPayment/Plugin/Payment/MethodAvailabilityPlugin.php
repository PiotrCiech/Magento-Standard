<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Plugin\Payment;

use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Rokezzz\ShippingPayment\Model\PaymentAvailability;

final class MethodAvailabilityPlugin
{
    public function __construct(
        private readonly PaymentAvailability $paymentAvailability
    ) {
    }

    public function afterIsAvailable(
        MethodInterface $subject,
        bool $result,
        ?CartInterface $quote = null
    ): bool {
        if (!$result) {
            return false;
        }

        return $this->paymentAvailability->isPaymentAllowed($subject->getCode(), $quote);
    }
}
