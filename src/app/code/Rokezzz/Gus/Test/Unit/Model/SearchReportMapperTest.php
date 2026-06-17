<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Test\Unit\Model;

use GusApi\SearchReport;
use GusApi\Type\Response\SearchResponseCompanyData;
use PHPUnit\Framework\TestCase;
use Rokezzz\Gus\Model\SearchReportMapper;

class SearchReportMapperTest extends TestCase
{
    public function testMapUsesReportNipWhenPresent(): void
    {
        $report = new SearchReport($this->createCompanyData(nip: '5261040828'));

        $companyData = (new SearchReportMapper())->map($report, '0000000000');

        $this->assertSame('5261040828', $companyData->getNip());
        $this->assertSame('ZAKŁAD TEST', $companyData->getName());
        $this->assertSame('Wrocław', $companyData->getCity());
    }

    public function testMapUsesFallbackNipWhenReportNipIsEmpty(): void
    {
        $report = new SearchReport($this->createCompanyData(nip: ''));

        $companyData = (new SearchReportMapper())->map($report, '5261040828');

        $this->assertSame('5261040828', $companyData->getNip());
    }

    private function createCompanyData(string $nip): SearchResponseCompanyData
    {
        $companyData = new SearchResponseCompanyData();
        $companyData->Regon = '02092251199990';
        $companyData->Nip = $nip;
        $companyData->StatusNip = 'U';
        $companyData->Nazwa = 'ZAKŁAD TEST';
        $companyData->Wojewodztwo = 'DOLNOŚLĄSKIE';
        $companyData->Powiat = 'm. Wrocław';
        $companyData->Gmina = 'Wrocław-Stare Miasto';
        $companyData->Miejscowosc = 'Wrocław';
        $companyData->KodPocztowy = '50-038';
        $companyData->Ulica = 'ul. Test-Krucza';
        $companyData->NrNieruchomosci = '33';
        $companyData->NrLokalu = '34B';
        $companyData->Typ = 'P';
        $companyData->SilosID = '6';
        $companyData->DataZakonczeniaDzialalnosci = '';
        $companyData->MiejscowoscPoczty = 'Wrocław';

        return $companyData;
    }
}
