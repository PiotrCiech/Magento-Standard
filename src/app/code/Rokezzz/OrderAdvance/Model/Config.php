<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ORDER_NUMBER_ENABLED = 'rokezzz_order_advance/order_number/enabled';
    private const XML_PATH_ORDER_NUMBER_LABEL = 'rokezzz_order_advance/order_number/label';
    private const XML_PATH_ORDER_NUMBER_PLACEHOLDER = 'rokezzz_order_advance/order_number/placeholder';
    private const XML_PATH_ORDER_NUMBER_MAX_LENGTH = 'rokezzz_order_advance/order_number/max_length';
    private const XML_PATH_STATUS_BAR_ENABLED = 'rokezzz_order_advance/status_bar/enabled';
    private const XML_PATH_STATUS_BAR_VISIBLE_DAYS = 'rokezzz_order_advance/status_bar/visible_days';
    private const DEFAULT_MAX_LENGTH = 64;
    private const DEFAULT_VISIBLE_DAYS = 14;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isOrderNumberEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ORDER_NUMBER_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getOrderNumberLabel(?int $storeId = null): string
    {
        $label = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_NUMBER_LABEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        return $label !== '' ? $label : (string) __('Your order number');
    }

    public function getOrderNumberPlaceholder(?int $storeId = null): string
    {
        return trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_NUMBER_PLACEHOLDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getOrderNumberMaxLength(?int $storeId = null): int
    {
        $maxLength = (int) $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_NUMBER_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $maxLength > 0 ? $maxLength : self::DEFAULT_MAX_LENGTH;
    }

    public function isStatusBarEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STATUS_BAR_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getStatusBarVisibleDays(?int $storeId = null): int
    {
        $visibleDays = (int) $this->scopeConfig->getValue(
            self::XML_PATH_STATUS_BAR_VISIBLE_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $visibleDays > 0 ? $visibleDays : self::DEFAULT_VISIBLE_DAYS;
    }
}
