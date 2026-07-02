<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Api;

interface ListingFragmentRendererInterface
{
    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    public function renderCategory(int $categoryId): array;

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    public function renderSearch(): array;
}
