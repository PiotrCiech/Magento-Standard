<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Test\Unit\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Search\Model\QueryFactory;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rokezzz\CatalogListing\Model\ListingParamsSanitizer;

final class ListingParamsSanitizerTest extends TestCase
{
    private RequestInterface&Stub $request;
    private UrlInterface&Stub $urlBuilder;
    private ListingParamsSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->request = $this->createStub(RequestInterface::class);
        $this->urlBuilder = $this->createStub(UrlInterface::class);
        $this->sanitizer = new ListingParamsSanitizer($this->request, $this->urlBuilder);
    }

    public function testSanitizeDropsInternalAndMalformedKeys(): void
    {
        $params = [
            'id' => 12,
            '_' => 'ajax',
            'p' => '2',
            'product_list_order=price' => '1',
        ];

        $this->assertSame(['p' => '2'], $this->sanitizer->sanitize($params));
    }

    public function testBuildCurrentListingUrlAppendsSanitizedQuery(): void
    {
        $this->request->method('getParams')->willReturn([
            'id' => 5,
            'p' => '2',
        ]);

        $this->assertSame(
            'https://example.test/cat.html?p=2',
            $this->sanitizer->buildCurrentListingUrl('https://example.test/cat.html')
        );
    }

    public function testBuildSearchListingUrlSetsQueryText(): void
    {
        $this->request->method('getParams')->willReturn([
            QueryFactory::QUERY_VAR_NAME => 'old',
            'p' => '3',
        ]);
        $this->urlBuilder->method('getUrl')->willReturnCallback(
            static function (string $route, array $params): string {
                return $route . '?' . http_build_query($params['_query']);
            }
        );

        $this->assertSame(
            'catalogsearch/result/index?p=3&' . QueryFactory::QUERY_VAR_NAME . '=shoes',
            $this->sanitizer->buildSearchListingUrl('shoes')
        );
    }
}
