<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\ScopeInterface;

final class ShippingMethodOptions
{
    public function __construct(
        private readonly ShippingConfig $shippingConfig,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function toOptionArray(?int $storeId = null): array
    {
        $options = [];

        foreach ($this->shippingConfig->getAllCarriers($storeId) as $carrierCode => $carrierModel) {
            $allowedMethods = $carrierModel->getAllowedMethods();

            if (!is_array($allowedMethods) || $allowedMethods === []) {
                continue;
            }

            $carrierTitle = (string) $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            if ($carrierTitle === '') {
                $carrierTitle = (string) $carrierCode;
            } else {
                $carrierTitle = (string) __($carrierTitle);
            }

            foreach ($allowedMethods as $methodCode => $methodTitle) {
                $value = $carrierCode . '_' . $methodCode;
                $methodLabel = is_string($methodTitle) && $methodTitle !== ''
                    ? (string) __($methodTitle)
                    : (string) $methodCode;

                $options[] = [
                    'value' => $value,
                    'label' => $carrierTitle . ' - ' . $methodLabel . ' (' . $value . ')',
                ];
            }
        }

        usort(
            $options,
            static fn (array $left, array $right): int => strcasecmp($left['label'], $right['label'])
        );

        return $options;
    }
}
