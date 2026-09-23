<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Orders\Model\Config;

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

        $result['rokezzzOrderNotes'] = [
            'enabled' => $this->config->isEnabled($storeId),
            'label' => $this->config->getLabel($storeId),
            'placeholder' => $this->config->getPlaceholder($storeId),
            'maxLength' => $this->config->getMaxLength($storeId),
        ];

        return $result;
    }
}
