<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use GusApi\SearchReport;
use Rokezzz\Gus\Api\Data\CompanyDataInterface;
use Rokezzz\Gus\Model\Data\CompanyData;

class SearchReportMapper
{
    public function map(SearchReport $report, string $fallbackNip): CompanyDataInterface
    {
        return new CompanyData(
            nip: $report->getNip() ?: $fallbackNip,
            regon: $report->getRegon(),
            name: $report->getName(),
            street: $report->getStreet(),
            propertyNumber: $report->getPropertyNumber(),
            apartmentNumber: $report->getApartmentNumber(),
            city: $report->getCity(),
            zipCode: $report->getZipCode(),
            province: $report->getProvince(),
            district: $report->getDistrict(),
            community: $report->getCommunity()
        );
    }
}
