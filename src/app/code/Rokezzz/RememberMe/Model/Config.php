<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_CUSTOMER_LIFETIME_WITHOUT = 'rokezzz_settings/remember_me/customer_lifetime_without';
    private const XML_PATH_CUSTOMER_LIFETIME_WITH = 'rokezzz_settings/remember_me/customer_lifetime_with';
    private const XML_PATH_ADMIN_LIFETIME_WITHOUT = 'rokezzz_settings/remember_me/admin_lifetime_without';
    private const XML_PATH_ADMIN_LIFETIME_WITH = 'rokezzz_settings/remember_me/admin_lifetime_with';

    private const DEFAULT_LIFETIME_WITHOUT = 1800;
    private const DEFAULT_LIFETIME_WITH = 1209600;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getCustomerLifetimeWithoutRemember(?int $storeId = null): int
    {
        return $this->getPositiveInt(self::XML_PATH_CUSTOMER_LIFETIME_WITHOUT, self::DEFAULT_LIFETIME_WITHOUT, $storeId);
    }

    public function getCustomerLifetimeWithRemember(?int $storeId = null): int
    {
        return $this->getPositiveInt(self::XML_PATH_CUSTOMER_LIFETIME_WITH, self::DEFAULT_LIFETIME_WITH, $storeId);
    }

    public function getAdminLifetimeWithoutRemember(): int
    {
        return $this->getPositiveInt(self::XML_PATH_ADMIN_LIFETIME_WITHOUT, self::DEFAULT_LIFETIME_WITHOUT);
    }

    public function getAdminLifetimeWithRemember(): int
    {
        return $this->getPositiveInt(self::XML_PATH_ADMIN_LIFETIME_WITH, self::DEFAULT_LIFETIME_WITH);
    }

    private function getPositiveInt(string $path, int $default, ?int $storeId = null): int
    {
        $scope = is_null($storeId) ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORE;
        $value = (int) $this->scopeConfig->getValue($path, $scope, $storeId);

        return $value > 0 ? $value : $default;
    }
}
