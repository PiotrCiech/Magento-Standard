<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\DeliveryDate\Model\Config;

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

        $result['rokezzzDeliveryDate'] = [
            'enabled' => $this->config->isEnabled($storeId),
            'label' => $this->config->getLabel($storeId),
            'required' => $this->config->isRequired($storeId),
            'minDate' => $this->config->getMinDate($storeId),
            'maxDate' => $this->config->getMaxDate($storeId),
            'excludeWeekends' => $this->config->isExcludeWeekends($storeId),
        ];

        return $result;
    }
}
