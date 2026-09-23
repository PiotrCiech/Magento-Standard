<?php

declare(strict_types=1);

namespace Rokezzz\ChatBot\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_chat_bot/general/enabled';
    private const XML_PATH_PROPERTY_ID = 'rokezzz_chat_bot/general/property_id';
    private const XML_PATH_WIDGET_ID = 'rokezzz_chat_bot/general/widget_id';
    private const XML_PATH_AUTO_START = 'rokezzz_chat_bot/behavior/auto_start';
    private const XML_PATH_HIDE_ON_LOAD = 'rokezzz_chat_bot/behavior/hide_on_load';
    private const XML_PATH_Z_INDEX = 'rokezzz_chat_bot/behavior/z_index';
    private const ID_PATTERN = '/^[A-Za-z0-9]+$/';
    private const DEFAULT_WIDGET_ID = 'default';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isActive(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId) && $this->getPropertyId($storeId) !== '';
    }

    public function getPropertyId(?int $storeId = null): string
    {
        return $this->getSanitizedId(self::XML_PATH_PROPERTY_ID, $storeId);
    }

    public function getWidgetId(?int $storeId = null): string
    {
        $widgetId = $this->getSanitizedId(self::XML_PATH_WIDGET_ID, $storeId);

        return $widgetId === '' ? self::DEFAULT_WIDGET_ID : $widgetId;
    }

    public function isAutoStart(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AUTO_START,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isHiddenOnLoad(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_ON_LOAD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getZIndex(?int $storeId = null): ?int
    {
        $zIndex = trim((string) $this->scopeConfig->getValue(
            self::XML_PATH_Z_INDEX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($zIndex === '' || !ctype_digit($zIndex)) {
            return null;
        }

        return (int) $zIndex;
    }

    private function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    private function getSanitizedId(string $configPath, ?int $storeId = null): string
    {
        $value = trim((string) $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if ($value === '' || !preg_match(self::ID_PATTERN, $value)) {
            return '';
        }

        return $value;
    }
}
