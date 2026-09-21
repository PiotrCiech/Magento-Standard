<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Model;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Settings\Api\Data\SellerDataInterface;
use Rokezzz\Settings\Api\SellerDataProviderInterface;
use Rokezzz\Settings\Model\Data\SellerData;

class SellerDataProvider implements SellerDataProviderInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function get(?int $storeId = null): SellerDataInterface
    {
        return new SellerData(
            companyName: $this->config->getCompanyName($storeId),
            nip: $this->config->getNip($storeId),
            regon: $this->config->getRegon($storeId),
            streetAddress: $this->config->getStreetAddress($storeId),
            cityPostcode: $this->config->getCityPostcode($storeId),
            countryCode: $this->config->getCountryCode($storeId),
            placeOfIssue: $this->config->getPlaceOfIssue($storeId),
            bankAccountNumber: $this->config->getBankAccountNumber($storeId),
            contactEmail: $this->config->getContactEmail($storeId),
            contactPhone: $this->config->getContactPhone($storeId),
            websiteUrl: $this->config->getWebsiteUrl($storeId),
            logoUrl: $this->resolveLogoUrl($storeId),
            systemInfo: $this->config->getSystemInfo($storeId)
        );
    }

    private function resolveLogoUrl(?int $storeId): string
    {
        $logoFilename = $this->config->getLogoFilename($storeId);

        if ($logoFilename === '') {
            return '';
        }

        $store = $this->storeManager->getStore($storeId);
        if (!$store instanceof Store) {
            return '';
        }

        $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $directory = trim($this->config->getLogoMediaDirectory(), '/');

        return $mediaBaseUrl . $directory . '/' . ltrim($logoFilename, '/');
    }
}
