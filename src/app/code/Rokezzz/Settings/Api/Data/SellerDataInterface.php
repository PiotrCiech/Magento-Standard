<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Api\Data;

interface SellerDataInterface
{
    public function getCompanyName(): string;

    public function getNip(): string;

    public function getRegon(): string;

    public function getStreetAddress(): string;

    public function getCityPostcode(): string;

    public function getCountryCode(): string;

    public function getPlaceOfIssue(): string;

    public function getBankAccountNumber(): string;

    public function getContactEmail(): string;

    public function getContactPhone(): string;

    public function getWebsiteUrl(): string;

    public function getLogoUrl(): string;

    public function getSystemInfo(): string;
}
