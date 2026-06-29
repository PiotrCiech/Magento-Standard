<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Api\Data;

interface CompanyDataInterface
{
    public function getNip(): string;

    public function getRegon(): string;

    public function getName(): string;

    public function getStreet(): string;

    public function getPropertyNumber(): string;

    public function getApartmentNumber(): string;

    public function getCity(): string;

    public function getZipCode(): string;

    public function getProvince(): string;

    public function getDistrict(): string;

    public function getCommunity(): string;
}
