<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\CatalogSearch\Helper\Data as CatalogSearchHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use LogicException;
use Rokezzz\CatalogListing\Api\ListingFragmentRendererInterface;

class ListingFragmentRenderer implements ListingFragmentRendererInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly Registry $registry,
        private readonly Resolver $layerResolver,
        private readonly CatalogSession $catalogSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly Design $catalogDesign,
        private readonly QueryFactory $queryFactory,
        private readonly CatalogSearchHelper $catalogSearchHelper,
        private readonly UrlInterface $urlBuilder,
        private readonly RequestInterface $request,
        private readonly ToolbarMemorizer $toolbarMemorizer,
        private readonly EventManager $eventManager,
        private readonly CategoryLoader $categoryLoader,
        private readonly CategoryLayoutHandleResolver $layoutHandleResolver,
        private readonly ListingParamsSanitizer $paramsSanitizer,
        private readonly FragmentHtmlCollector $fragmentHtmlCollector,
    ) {
    }

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    public function renderCategory(int $categoryId): array
    {
        $category = $this->categoryLoader->load($categoryId);

        if (is_null($category)) {
            return $this->emptyResponse();
        }

        $this->initializeCategoryContext($category);

        $page = $this->pageFactory->create();
        $page->getConfig()->setPageLayout('2columns-left');

        $settings = $this->catalogDesign->getDesignSettings($category);

        if ($settings->getPageLayout()) {
            $page->getConfig()->setPageLayout($settings->getPageLayout());
        }

        $page->addHandle('rokezzz_cataloglisting_category_fragment');
        $this->layoutHandleResolver->apply($page, $category);

        return $this->fragmentHtmlCollector->collect(
            $page,
            'category.products',
            'catalog.leftnav',
            (string) $category->getUrl(),
            'rokezzz_cataloglisting/category/fragment'
        );
    }

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    public function renderSearch(): array
    {
        $queryText = $this->resolveSearchQueryText();

        if ($queryText === '') {
            return $this->emptySearchResponse();
        }

        $query = $this->queryFactory->get();
        if (!$query instanceof Query) {
            throw new LogicException('Search query is not available.');
        }

        $query->setQueryText($queryText);
        $query->setStoreId((int) $this->storeManager->getStore()->getId());
        $this->toolbarMemorizer->memorizeParams();
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

        $page = $this->pageFactory->create();
        $page->getConfig()->setPageLayout('2columns-left');
        $page->addHandle('rokezzz_cataloglisting_search_fragment');

        if ($query->getNumResults() === 0) {
            $page->addHandle('catalogsearch_result_index_noresults');
        }

        $this->catalogSearchHelper->checkNotes();

        return $this->fragmentHtmlCollector->collect(
            $page,
            'search.result',
            'catalogsearch.leftnav',
            $this->paramsSanitizer->buildSearchListingUrl($queryText),
            'rokezzz_cataloglisting/search/fragment'
        );
    }

    private function initializeCategoryContext(Category $category): void
    {
        if ($this->registry->registry('current_category')) {
            $this->registry->unregister('current_category');
        }

        $this->registry->register('current_category', $category);
        $this->catalogSession->setLastVisitedCategoryId($category->getId());
        $this->catalogSession->setLastViewedCategoryId($category->getId());
        $this->toolbarMemorizer->memorizeParams();
        $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);

        $this->eventManager->dispatch(
            'catalog_controller_category_init_after',
            ['category' => $category]
        );
    }

    private function resolveSearchQueryText(): string
    {
        $queryText = trim((string) $this->request->getParam(QueryFactory::QUERY_VAR_NAME));

        if ($queryText !== '') {
            return $queryText;
        }

        if ($this->request instanceof HttpRequest) {
            $queryText = trim((string) $this->request->getQueryValue(QueryFactory::QUERY_VAR_NAME));

            if ($queryText !== '') {
                return $queryText;
            }
        }

        return '';
    }

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    private function emptySearchResponse(): array
    {
        $queryText = $this->resolveSearchQueryText();

        return [
            'productsHtml' => '',
            'filtersHtml' => '',
            'url' => $queryText === ''
                ? $this->urlBuilder->getUrl('catalogsearch/result/index')
                : $this->paramsSanitizer->buildSearchListingUrl($queryText),
            'pageTitle' => '',
        ];
    }

    /**
     * @return array{productsHtml: string, filtersHtml: string, url: string, pageTitle: string}
     */
    private function emptyResponse(): array
    {
        return [
            'productsHtml' => '',
            'filtersHtml' => '',
            'url' => $this->paramsSanitizer->buildCurrentListingUrl(),
            'pageTitle' => '',
        ];
    }
}
