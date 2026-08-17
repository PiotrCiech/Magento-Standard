<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_invoice/general/enabled';
    private const XML_PATH_KSEF_ENABLED = 'rokezzz_invoice/ksef/enabled';
    private const XML_PATH_KSEF_START_DATE = 'rokezzz_invoice/ksef/start_date';
    private const XML_PATH_INVOICES_DIRECTORY = 'rokezzz_invoice/files/invoices_directory';

    private const DEFAULT_KSEF_START_DATE = '2026-01-01';
    private const DEFAULT_INVOICES_DIRECTORY = 'rokezzz/invoices';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isKsefEnabled(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_KSEF_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getKsefStartDate(?int $storeId = null): string
    {
        $startDate = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_KSEF_START_DATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($startDate === '') {
            return self::DEFAULT_KSEF_START_DATE;
        }

        return $startDate;
    }

    public function getInvoicesDirectory(?int $storeId = null): string
    {
        $directory = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_INVOICES_DIRECTORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($directory === '') {
            return self::DEFAULT_INVOICES_DIRECTORY;
        }

        return trim($directory, '/');
    }
}
