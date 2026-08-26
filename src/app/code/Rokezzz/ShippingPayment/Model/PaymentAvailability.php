<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Shipping\Model\Config as ShippingConfig;

final class PaymentAvailability
{
    public function __construct(
        private readonly Config $config,
        private readonly ShippingConfig $shippingConfig
    ) {
    }

    public function isPaymentAllowed(string $paymentMethodCode, ?CartInterface $quote): bool
    {
        if (!$this->config->isEnabled() || !$quote instanceof Quote || $quote->isVirtual()) {
            return true;
        }

        $carrierCode = $this->resolveCarrierCode($quote);

        if ($carrierCode === '') {
            return true;
        }

        $allowedMethods = $this->config->getAllowedPaymentMethodCodes(
            $carrierCode,
            (int) $quote->getStoreId()
        );

        return is_null($allowedMethods) || in_array($paymentMethodCode, $allowedMethods, true);
    }

    private function resolveCarrierCode(Quote $quote): string
    {
        $shippingMethod = trim((string) $quote->getShippingAddress()->getShippingMethod());

        if ($shippingMethod === '') {
            return '';
        }

        $rate = $quote->getShippingAddress()->getShippingRateByCode($shippingMethod);

        if (!is_null($rate)) {
            $carrier = trim((string) $rate->getCarrier());

            if ($carrier !== '') {
                return $carrier;
            }
        }

        $matched = '';

        foreach (array_keys($this->shippingConfig->getActiveCarriers((int) $quote->getStoreId())) as $carrierCode) {
            $carrierCode = (string) $carrierCode;

            if (
                ($shippingMethod === $carrierCode || str_starts_with($shippingMethod, $carrierCode . '_'))
                && strlen($carrierCode) > strlen($matched)
            ) {
                $matched = $carrierCode;
            }
        }

        return $matched;
    }
}
