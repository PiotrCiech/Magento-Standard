<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Test\Unit\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Plugin\Checkout\LayoutProcessorPlugin;

final class LayoutProcessorPluginTest extends TestCase
{
    private Config&MockObject $config;

    private StoreManagerInterface&MockObject $storeManager;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->storeManager->method('getStore')->willReturn(
            $this->createConfiguredMock(Store::class, ['getId' => 1])
        );
    }

    public function testAfterProcessAppliesConfigWhenEnabled(): void
    {
        $this->config->method('isEnabled')->willReturn(true);
        $this->config->method('getLabel')->willReturn('Uwagi do zamówienia');
        $this->config->method('getPlaceholder')->willReturn('Dodatkowe informacje dla kuriera...');
        $this->config->method('getMaxLength')->willReturn(1000);

        $jsLayout = $this->createJsLayout();
        $plugin = new LayoutProcessorPlugin($this->config, $this->storeManager);
        $result = $plugin->afterProcess($this->createStub(LayoutProcessor::class), $jsLayout);

        $this->assertSame(
            'Uwagi do zamówienia',
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['rokezzz-order-comment-shipping']['config']['label']
        );
        $this->assertSame(
            'Dodatkowe informacje dla kuriera...',
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['rokezzz-order-comment-shipping']['config']['placeholder']
        );
    }

    public function testAfterProcessRemovesComponentsWhenDisabled(): void
    {
        $this->config->method('isEnabled')->willReturn(false);

        $jsLayout = $this->createJsLayout();
        $plugin = new LayoutProcessorPlugin($this->config, $this->storeManager);
        $result = $plugin->afterProcess($this->createStub(LayoutProcessor::class), $jsLayout);

        $this->assertArrayNotHasKey(
            'rokezzz-order-comment-shipping',
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']
        );
        $this->assertArrayNotHasKey(
            'rokezzz-order-comment-payment',
            $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function createJsLayout(): array
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'rokezzz-order-comment-shipping' => [
                                                    'component' => 'Rokezzz_Orders/js/view/order-comment',
                                                    'config' => [
                                                        'step' => 'shipping',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'rokezzz-order-comment-payment' => [
                                                    'component' => 'Rokezzz_Orders/js/view/order-comment',
                                                    'config' => [
                                                        'step' => 'payment',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
