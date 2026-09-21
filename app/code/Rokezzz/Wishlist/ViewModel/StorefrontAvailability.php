<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

final class StorefrontAvailability implements ArgumentInterface
{
    public function __construct(
        private readonly WishlistHelper $wishlistHelper
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->wishlistHelper->isAllow();
    }
}
