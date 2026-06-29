<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_gus/general/enabled';
    private const XML_PATH_API_KEY = 'rokezzz_gus/general/api_key';
    private const XML_PATH_ENVIRONMENT = 'rokezzz_gus/general/environment';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
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

    public function getApiKey(?int $storeId = null): string
    {
        $encryptedKey = (string) $this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($encryptedKey === '') {
            return '';
        }

        return $this->encryptor->decrypt($encryptedKey);
    }

    public function getEnvironment(?int $storeId = null): string
    {
        $environment = (string) $this->scopeConfig->getValue(
            self::XML_PATH_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $environment === 'dev' ? 'dev' : 'prod';
    }
}
