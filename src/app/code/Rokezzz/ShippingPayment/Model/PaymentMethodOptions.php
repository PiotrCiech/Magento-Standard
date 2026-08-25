<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Store\Model\ScopeInterface;

final class PaymentMethodOptions
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly PaymentConfig $paymentConfig
    ) {
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function toOptionArray(?int $storeId = null): array
    {
        $titles = [];

        foreach ($this->paymentConfig->getActiveMethods() as $code => $method) {
            $titles[(string) $code] = (string) $method->getTitle();
        }

        $options = [];
        $methods = $this->scopeConfig->getValue(
            'payment',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!is_array($methods)) {
            return $options;
        }

        foreach ($methods as $code => $method) {
            if (!is_array($method) || !isset($method['model'])) {
                continue;
            }

            $code = (string) $code;

            if (isset($titles[$code]) && $titles[$code] !== '') {
                $title = $titles[$code];
            } elseif (isset($method['title'])) {
                $title = (string) __($method['title']);
            } else {
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
