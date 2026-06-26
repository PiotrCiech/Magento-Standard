<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

use Magento\Catalog\Block\Category\View as CategoryViewBlock;
use Magento\CatalogSearch\Block\Result as SearchResultBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\Page;
use LogicException;

class FragmentHtmlCollector
{
    public function __construct(
        private readonly ListingParamsSanitizer $paramsSanitizer,
        private readonly FragmentUrlRewriter $urlRewriter,
    ) {
    }

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    public function collect(
        Page $page,
        string $productsBlockName,
        string $filtersBlockName,
        string $baseUrl,
        string $fragmentRoute
    ): array {
        $page->initLayout();

        $pageConfig = $page->getConfig();
        if (!$pageConfig instanceof PageConfig) {
            throw new LogicException('Page configuration is not available.');
        }
        $pageConfig->publicBuild();

        $layout = $page->getLayout();
        if (!$layout instanceof Layout) {
            throw new LogicException('Page layout is not available.');
        }
        $layout->publicBuild();

        $productsBlock = $layout->getBlock($productsBlockName);
        $filtersBlock = $layout->getBlock($filtersBlockName);

        $productsHtml = $this->resolveProductsHtml($productsBlock);
        $listingUrl = $this->paramsSanitizer->buildCurrentListingUrl($baseUrl);
        $filtersHtml = $filtersBlock ? (string) $filtersBlock->toHtml() : '';

        return [
            'productsHtml' => $this->urlRewriter->rewrite($productsHtml, $listingUrl, $fragmentRoute),
            'filtersHtml' => $this->urlRewriter->rewrite($filtersHtml, $listingUrl, $fragmentRoute),
            'url' => $listingUrl,
            'pageTitle' => (string) $page->getConfig()->getTitle()->get(),
        ];
    }

    private function resolveProductsHtml(BlockInterface|false $productsBlock): string
    {
        if ($productsBlock instanceof CategoryViewBlock) {
            return (string) $productsBlock->getProductListHtml();
        }

        if ($productsBlock instanceof SearchResultBlock) {
            $productsHtml = (string) $productsBlock->toHtml();

            if ($productsHtml === '') {
                $listHtml = (string) $productsBlock->getProductListHtml();

                if ($listHtml !== '') {
                    return '<div class="search results">' . $listHtml . '</div>';
                }
            }

            return $productsHtml;
        }

        if ($productsBlock instanceof BlockInterface) {
            return (string) $productsBlock->toHtml();
        }

        return '';
    }
}
