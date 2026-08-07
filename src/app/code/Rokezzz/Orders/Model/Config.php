<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_orders/order_notes/enabled';
    private const XML_PATH_MAX_LENGTH = 'rokezzz_orders/order_notes/max_length';
    private const XML_PATH_LABEL = 'rokezzz_orders/order_notes/label';
    private const XML_PATH_PLACEHOLDER = 'rokezzz_orders/order_notes/placeholder';

    private const DEFAULT_MAX_LENGTH = 1000;

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

    public function getMaxLength(?int $storeId = null): int
    {
        $maxLength = (int) $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $maxLength > 0 ? $maxLength : self::DEFAULT_MAX_LENGTH;
    }

    public function getLabel(?int $storeId = null): string
    {
        $label = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_LABEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        return $label !== '' ? $label : (string) __('Order notes');
    }

    public function getPlaceholder(?int $storeId = null): string
    {
        return trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_PLACEHOLDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }
}
