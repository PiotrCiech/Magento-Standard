<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\ViewModel;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class HeaderSearchConfig implements ArgumentInterface
{
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly Json $json,
    ) {
    }

    public function getSearchFragmentUrl(): string
    {
        return $this->urlBuilder->getUrl('rokezzz_cataloglisting/search/fragment');
    }

    public function getSearchResultUrl(): string
    {
        return $this->urlBuilder->getUrl('catalogsearch/result/index');
    }

    public function getJsonConfig(): string
    {
        return $this->json->serialize([
            'searchFragmentUrl' => $this->getSearchFragmentUrl(),
            'searchResultUrl' => $this->getSearchResultUrl(),
        ]);
    }
}
