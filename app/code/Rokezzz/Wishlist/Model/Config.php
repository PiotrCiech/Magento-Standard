<?php

declare(strict_types=1);

namespace Rokezzz\Wishlist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_SHARING_ENABLED = 'rokezzz_wishlist/general/sharing_enabled';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isSharingEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHARING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
