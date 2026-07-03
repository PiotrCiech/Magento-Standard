<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\Plugin\Wishlist\Block;

use Magento\Wishlist\Block\Customer\Wishlist\Button;
use Rokezzz\Wishlist\Model\Config;

class HideWishlistShareButtonPlugin
{
    private const SHARE_BUTTON_BLOCK_NAME = 'customer.wishlist.button.share';

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function aroundToHtml(Button $subject, callable $proceed): string
    {
        if ($this->canRenderButton($subject)) {
            return $proceed();
        }

        return '';
    }

    private function canRenderButton(Button $subject): bool
    {
        if ($subject->getNameInLayout() !== self::SHARE_BUTTON_BLOCK_NAME) {
            return true;
        }

        return $this->config->isSharingEnabled();
    }
}
