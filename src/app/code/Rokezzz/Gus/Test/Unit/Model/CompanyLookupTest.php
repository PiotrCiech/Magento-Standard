<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Test\Unit\Model;

use GusApi\SearchReport;
use GusApi\Type\Response\SearchResponseCompanyData;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\Gus\Api\Data\CompanyDataInterface;
use Rokezzz\Gus\Model\CompanyLookup;
use Rokezzz\Gus\Model\Config;
use Rokezzz\Gus\Model\NipNormalizer;
use Rokezzz\Gus\Model\RegonApiClient;
use Rokezzz\Gus\Model\SearchReportMapper;

class CompanyLookupTest extends TestCase
{
    public function testFindByNipThrowsWhenFeatureDisabled(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(false);

        $lookup = new CompanyLookup(
            $config,
            new NipNormalizer(),
            $this->createStub(RegonApiClient::class),
            new SearchReportMapper()
        );

        $this->expectException(LocalizedException::class);

        $lookup->findByNip('526-104-08-28');
    }

    public function testFindByNipThrowsWhenApiKeyMissing(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('getApiKey')->willReturn('');

        $lookup = new CompanyLookup(
            $config,
            new NipNormalizer(),
            $this->createStub(RegonApiClient::class),
            new SearchReportMapper()
        );

        $this->expectException(LocalizedException::class);

        $lookup->findByNip('526-104-08-28');
    }

    public function testFindByNipReturnsMappedCompanyData(): void
    {
        $companyData = new SearchResponseCompanyData();
        $companyData->Regon = '02092251199990';
        $companyData->Nip = '5261040828';
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

        $report = new SearchReport($companyData);

        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn(true);
        $config->method('getApiKey')->willReturn('api-key');
        $config->method('getEnvironment')->willReturn('test');

        $regonApiClient = $this->createMock(RegonApiClient::class);
        $regonApiClient->method('fetchByNip')
            ->with('api-key', 'test', '5261040828')
            ->willReturn($report);

        $result = (new CompanyLookup(
            $config,
            new NipNormalizer(),
            $regonApiClient,
            new SearchReportMapper()
        ))->findByNip('526-104-08-28');

        $this->assertInstanceOf(CompanyDataInterface::class, $result);
        $this->assertSame('5261040828', $result->getNip());
        $this->assertSame('ZAKŁAD TEST', $result->getName());
    }
}
