<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLE_NEW = 'rokezzz_dynamiccategories/general/enable_new';
    private const XML_PATH_ENABLE_BESTSELLERS = 'rokezzz_dynamiccategories/general/enable_bestsellers';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
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
