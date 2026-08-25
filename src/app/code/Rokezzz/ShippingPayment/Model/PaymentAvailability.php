<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

final class PaymentAvailability
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function isPaymentAllowed(string $paymentMethodCode, ?CartInterface $quote): bool
    {
        if (!$this->config->isEnabled() || !$quote instanceof Quote || $quote->isVirtual()) {
            return true;
        }

        $allowedMethods = $this->config->getAllowedPaymentMethodCodes(
            trim((string) $quote->getShippingAddress()->getShippingMethod()),
            (int) $quote->getStoreId()
        );

        return is_null($allowedMethods) || in_array($paymentMethodCode, $allowedMethods, true);
    }
}
