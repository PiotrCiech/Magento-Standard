<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\OrderAdvance\Model\Config;

final class DefaultConfigProviderPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function afterGetConfig(DefaultConfigProvider $subject, array $result): array
    {
        $storeId = (int) $this->storeManager->getStore()->getId();

        $result['rokezzzCustomerOrderNumber'] = [
            'enabled' => $this->config->isOrderNumberEnabled($storeId),
            'label' => $this->config->getOrderNumberLabel($storeId),
            'placeholder' => $this->config->getOrderNumberPlaceholder($storeId),
            'maxLength' => $this->config->getOrderNumberMaxLength($storeId),
        ];

        return $result;
    }
}
