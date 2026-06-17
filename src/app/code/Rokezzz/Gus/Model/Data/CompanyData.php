<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model\Data;

use Rokezzz\Gus\Api\Data\CompanyDataInterface;

class CompanyData implements CompanyDataInterface
{
    public function __construct(
        private readonly string $nip = '',
        private readonly string $regon = '',
        private readonly string $name = '',
        private readonly string $street = '',
        private readonly string $propertyNumber = '',
        private readonly string $apartmentNumber = '',
        private readonly string $city = '',
        private readonly string $zipCode = '',
        private readonly string $province = '',
        private readonly string $district = '',
        private readonly string $community = ''
    ) {
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function getRegon(): string
    {
        return $this->regon;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPropertyNumber(): string
    {
        return $this->propertyNumber;
    }

    public function getApartmentNumber(): string
    {
        return $this->apartmentNumber;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCommunity(): string
    {
        return $this->community;
    }
}
