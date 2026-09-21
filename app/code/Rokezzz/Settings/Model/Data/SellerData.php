<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Model\Data;

use Rokezzz\Settings\Api\Data\SellerDataInterface;

class SellerData implements SellerDataInterface
{
    public function __construct(
        private readonly string $companyName = '',
        private readonly string $nip = '',
        private readonly string $regon = '',
        private readonly string $streetAddress = '',
        private readonly string $cityPostcode = '',
        private readonly string $countryCode = 'PL',
        private readonly string $placeOfIssue = '',
        private readonly string $bankAccountNumber = '',
        private readonly string $contactEmail = '',
        private readonly string $contactPhone = '',
        private readonly string $websiteUrl = '',
        private readonly string $logoUrl = '',
        private readonly string $systemInfo = ''
    ) {
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function getRegon(): string
    {
        return $this->regon;
    }

    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    public function getCityPostcode(): string
    {
        return $this->cityPostcode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getPlaceOfIssue(): string
    {
        return $this->placeOfIssue;
    }

    public function getBankAccountNumber(): string
    {
        return $this->bankAccountNumber;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    public function getSystemInfo(): string
    {
        return $this->systemInfo;
    }
}
