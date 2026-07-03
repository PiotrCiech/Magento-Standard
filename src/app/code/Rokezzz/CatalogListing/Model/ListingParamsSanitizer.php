<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Search\Model\QueryFactory;

class ListingParamsSanitizer
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly UrlInterface $urlBuilder,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function sanitize(array $params): array
    {
        unset($params['id'], $params['_']);

        foreach (array_keys($params) as $key) {
            if (!is_string($key) || str_contains($key, '=')) {
                unset($params[$key]);
            }
        }

        return $params;
    }

    public function buildCurrentListingUrl(string $baseUrl = ''): string
    {
        $params = $this->sanitize($this->request->getParams());

        if ($baseUrl !== '') {
            $separator = str_contains($baseUrl, '?') ? '&' : '?';
            $query = http_build_query($params);

            return $query === '' ? $baseUrl : $baseUrl . $separator . $query;
        }

        return $this->urlBuilder->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => $params,
        ]);
    }

    public function buildSearchListingUrl(string $queryText): string
    {
        $params = $this->sanitize($this->request->getParams());
        unset($params[QueryFactory::QUERY_VAR_NAME]);
        $params[QueryFactory::QUERY_VAR_NAME] = $queryText;

        return $this->urlBuilder->getUrl('catalogsearch/result/index', [
            '_query' => $params,
        ]);
    }
}
