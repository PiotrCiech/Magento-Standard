<?php

declare(strict_types=1);

namespace Rokezzz\Category\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLE_NEW = 'rokezzz_category/general/enable_new';
    private const XML_PATH_ENABLE_BESTSELLERS = 'rokezzz_category/general/enable_bestsellers';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isNewEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_NEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isBestsellersEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_BESTSELLERS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
