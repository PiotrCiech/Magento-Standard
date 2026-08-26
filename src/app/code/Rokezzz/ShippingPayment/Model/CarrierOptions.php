<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\ScopeInterface;

final class CarrierOptions
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

        foreach ($this->shippingConfig->getActiveCarriers($storeId) as $carrierCode => $carrierModel) {
            if (!$carrierModel instanceof CarrierInterface) {
                continue;
            }

            $allowedMethods = $carrierModel->getAllowedMethods();

            if (!is_array($allowedMethods) || $allowedMethods === []) {
                continue;
            }

            $carrierCode = (string) $carrierCode;
            $carrierTitle = (string) $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            if ($carrierTitle === '') {
                $carrierTitle = $carrierCode;
            } else {
                $carrierTitle = (string) __($carrierTitle);
            }

            $options[] = [
                'value' => $carrierCode,
                'label' => $carrierTitle . ' (' . $carrierCode . ')',
            ];
        }

        usort(
            $options,
            static fn (array $left, array $right): int => strcasecmp($left['label'], $right['label'])
        );

        return $options;
    }
}
