<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLE_PDF_CATALOG = 'rokezzz_categoryadvance/general/enable_pdf_catalog';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isPdfCatalogEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_PDF_CATALOG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
