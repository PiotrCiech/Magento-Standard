<?php

namespace Furgonetka\Pickups\Plugins\Checkout;

class LayoutProcessorPlugin
{

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array                                            $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional'] = [
            'component' => 'uiComponent',
            'displayArea' => 'shippingAdditional',
            'children' => [
                'furgonetka_pickup_point' => [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input',
                        'options' => [],
                        'id' => 'furgonetka_pickup_point',
                    ],
                    'dataScope' => 'shippingAddress.furgonetka_pickup_point',
                    'provider' => 'checkoutProvider',
                    'visible' => false,
                    'validation' => false,
                    'sortOrder' => 200,
                ],
            ],
        ];

        return $jsLayout;
    }

}
