<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Plugin\Customer\Block\Account;

use Magento\Customer\Block\Account\Navigation;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class HideArchivedOrdersNavPlugin
{
    private const NAV_BLOCK_NAME = 'customer-account-navigation-archived-orders-link';

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function beforeToHtml(Navigation $subject): void
    {
        if ($this->config->isEnabled()) {
            return;
        }

        if ($subject->getChildBlock(self::NAV_BLOCK_NAME)) {
            $subject->unsetChild(self::NAV_BLOCK_NAME);
        }
    }
}
