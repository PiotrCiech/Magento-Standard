<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\DeliveryDate\Model\Config;

final class LayoutProcessorPlugin
{
    private const SHIPPING_COMPONENT_PATH = [
        'components',
        'checkout',
        'children',
        'steps',
        'children',
        'shipping-step',
        'children',
        'shippingAddress',
        'children',
        'rokezzz-delivery-date-shipping',
    ];

    private const PAYMENT_COMPONENT_PATH = [
        'components',
        'checkout',
        'children',
        'steps',
        'children',
        'billing-step',
        'children',
        'payment',
        'children',
        'rokezzz-delivery-date-payment',
    ];

    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        if (!$this->config->isEnabled()) {
            $this->unsetChild($jsLayout, self::SHIPPING_COMPONENT_PATH);
            $this->unsetChild($jsLayout, self::PAYMENT_COMPONENT_PATH);

            return $jsLayout;
        }

        $storeId = (int) $this->storeManager->getStore()->getId();
        $this->applyComponentConfig($jsLayout, self::SHIPPING_COMPONENT_PATH, $storeId);
        $this->applyComponentConfig($jsLayout, self::PAYMENT_COMPONENT_PATH, $storeId);

        return $jsLayout;
    }

    /**
     * @param list<string> $path
     */
    private function applyComponentConfig(array &$jsLayout, array $path, int $storeId): void
    {
        $node = &$jsLayout;

        foreach ($path as $index => $segment) {
            if ($index === count($path) - 1) {
                if (!isset($node[$segment]) || !is_array($node[$segment])) {
                    return;
                }

                $node[$segment]['config'] = array_merge(
                    $node[$segment]['config'] ?? [],
                    [
                        'label' => $this->config->getLabel($storeId),
                        'required' => $this->config->isRequired($storeId),
                        'minDate' => $this->config->getMinDate($storeId),
                        'maxDate' => $this->config->getMaxDate($storeId),
                        'excludeWeekends' => $this->config->isExcludeWeekends($storeId),
                    ]
                );

                return;
            }

            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                return;
            }

            $node = &$node[$segment];
        }
    }

    /**
     * @param list<string> $path
     */
    private function unsetChild(array &$jsLayout, array $path): void
    {
        $node = &$jsLayout;

        foreach ($path as $index => $segment) {
            if ($index === count($path) - 1) {
                unset($node[$segment]);

                return;
            }

            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                return;
            }

            $node = &$node[$segment];
        }
    }
}
