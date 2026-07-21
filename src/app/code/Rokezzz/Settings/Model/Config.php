<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_COMPANY_NAME = 'rokezzz_settings/company/company_name';
    private const XML_PATH_NIP = 'rokezzz_settings/company/nip';
    private const XML_PATH_REGON = 'rokezzz_settings/company/regon';
    private const XML_PATH_STREET_ADDRESS = 'rokezzz_settings/company/street_address';
    private const XML_PATH_CITY_POSTCODE = 'rokezzz_settings/company/city_postcode';
    private const XML_PATH_COUNTRY_CODE = 'rokezzz_settings/company/country_code';
    private const XML_PATH_PLACE_OF_ISSUE = 'rokezzz_settings/company/place_of_issue';
    private const XML_PATH_BANK_ACCOUNT_NUMBER = 'rokezzz_settings/company/bank_account_number';
    private const XML_PATH_CONTACT_EMAIL = 'rokezzz_settings/company/contact_email';
    private const XML_PATH_CONTACT_PHONE = 'rokezzz_settings/company/contact_phone';
    private const XML_PATH_WEBSITE_URL = 'rokezzz_settings/company/website_url';
    private const XML_PATH_LOGO = 'rokezzz_settings/branding/logo';
    private const XML_PATH_KSEF_API_URL = 'rokezzz_settings/ksef/api_url';
    private const XML_PATH_KSEF_TOKEN = 'rokezzz_settings/ksef/token';
    private const XML_PATH_KSEF_NIP = 'rokezzz_settings/ksef/nip';

    private const DEFAULT_COUNTRY_CODE = 'PL';
    private const DEFAULT_SYSTEM_INFO = 'Magento';
    private const PLATFORM_SIGNATURE = 'System powered by Rokezzz';
    private const LOGO_MEDIA_DIRECTORY = 'rokezzz/settings';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function getCompanyName(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_COMPANY_NAME, $storeId);
    }

    public function getNip(?int $storeId = null): string
    {
        return $this->normalizeNip($this->getString(self::XML_PATH_NIP, $storeId));
    }

    public function getRegon(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_REGON, $storeId);
    }

    public function getStreetAddress(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_STREET_ADDRESS, $storeId);
    }

    public function getCityPostcode(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_CITY_POSTCODE, $storeId);
    }

    public function getCountryCode(?int $storeId = null): string
    {
        $countryCode = strtoupper($this->getString(self::XML_PATH_COUNTRY_CODE, $storeId));

        return $countryCode !== '' ? $countryCode : self::DEFAULT_COUNTRY_CODE;
    }

    public function getPlaceOfIssue(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_PLACE_OF_ISSUE, $storeId);
    }

    public function getBankAccountNumber(?int $storeId = null): string
    {
        return preg_replace('/\s+/', '', $this->getString(self::XML_PATH_BANK_ACCOUNT_NUMBER, $storeId)) ?? '';
    }

    public function getContactEmail(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_CONTACT_EMAIL, $storeId);
    }

    public function getContactPhone(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_CONTACT_PHONE, $storeId);
    }

    public function getWebsiteUrl(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_WEBSITE_URL, $storeId);
    }

    public function getLogoFilename(?int $storeId = null): string
    {
        return $this->getString(self::XML_PATH_LOGO, $storeId);
    }

    public function getLogoMediaDirectory(): string
    {
        return self::LOGO_MEDIA_DIRECTORY;
    }

    public function getSystemInfo(?int $storeId = null): string
    {
        return (string) __(self::PLATFORM_SIGNATURE);
    }

    public function getKsefApiUrl(?int $storeId = null): string
    {
        return rtrim($this->getString(self::XML_PATH_KSEF_API_URL, $storeId), '/');
    }

    public function getKsefToken(?int $storeId = null): string
    {
        $encryptedToken = $this->getString(self::XML_PATH_KSEF_TOKEN, $storeId);

        if ($encryptedToken === '') {
            return '';
        }

        return $this->encryptor->decrypt($encryptedToken);
    }

    public function getKsefNip(?int $storeId = null): string
    {
        $ksefNip = $this->normalizeNip($this->getString(self::XML_PATH_KSEF_NIP, $storeId));

        if ($ksefNip !== '') {
            return $ksefNip;
        }

        return $this->getNip($storeId);
    }

    private function getString(string $path, ?int $storeId): string
    {
        return trim((string) $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    private function normalizeNip(string $nip): string
    {
        return preg_replace('/\D+/', '', $nip) ?? '';
    }
}
