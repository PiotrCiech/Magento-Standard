<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\LatestOrderProvider;
use Rokezzz\OrderAdvance\Model\LatestOrderStatusDataBuilder;

final class LatestOrderStatus implements SectionSourceInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly CustomerSession $customerSession,
        private readonly LatestOrderProvider $latestOrderProvider,
        private readonly LatestOrderStatusDataBuilder $latestOrderStatusDataBuilder
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getSectionData(): array
    {
        if (!$this->config->isStatusBarEnabled() || !$this->customerSession->isLoggedIn()) {
            return ['visible' => false];
        }

        $order = $this->latestOrderProvider->getLatestOrder(
            (int) $this->customerSession->getCustomerId()
        );

        return $this->latestOrderStatusDataBuilder->build($order);
    }
}
