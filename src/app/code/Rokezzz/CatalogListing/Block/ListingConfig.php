<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Block;

use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ListingConfig extends Template
{
    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly UrlInterface $urlBuilder,
        private readonly Json $json,
        array $data = [],
    ) {
        parent::__construct($context, $data);
    }

    public function getListingType(): string
    {
        return (string) $this->getData('listing_type');
    }

    public function getCategoryId(): ?int
    {
        $category = $this->registry->registry('current_category');

        return $category ? (int) $category->getId() : null;
    }

    public function getFragmentUrl(): string
    {
        if ($this->getListingType() === 'search') {
            return $this->urlBuilder->getUrl('rokezzz_cataloglisting/search/fragment');
        }

        return $this->urlBuilder->getUrl('rokezzz_cataloglisting/category/fragment');
    }

    public function shouldRender(): bool
    {
        if ($this->getListingType() === 'category') {
            return $this->getCategoryId() !== null;
        }

        return $this->getListingType() === 'search';
    }

    public function getSearchFragmentUrl(): string
    {
        return $this->urlBuilder->getUrl('rokezzz_cataloglisting/search/fragment');
    }

    public function getSearchResultUrl(): string
    {
        return $this->urlBuilder->getUrl('catalogsearch/result');
    }

    public function getJsonConfig(): string
    {
        return $this->json->serialize([
            'listingType' => $this->getListingType(),
            'categoryId' => $this->getCategoryId(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'searchFragmentUrl' => $this->getSearchFragmentUrl(),
            'searchResultUrl' => $this->getSearchResultUrl(),
        ]);
    }
}
