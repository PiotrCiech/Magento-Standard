<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Payment\Model\Config as PaymentConfig;

final class PaymentMethodOptions
{
    public function __construct(
        private readonly PaymentConfig $paymentConfig
    ) {
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function toOptionArray(?int $storeId = null): array
    {
        $options = [];

        foreach ($this->paymentConfig->getActiveMethods($storeId) as $code => $method) {
            $code = (string) $code;
            $title = trim((string) $method->getTitle());

            if ($title === '') {
                $title = $code;
            }

            $options[] = [
                'value' => $code,
                'label' => $title . ' (' . $code . ')',
            ];
        }

        usort(
            $options,
            static fn (array $left, array $right): int => strcasecmp($left['label'], $right['label'])
        );

        return $options;
    }
}
