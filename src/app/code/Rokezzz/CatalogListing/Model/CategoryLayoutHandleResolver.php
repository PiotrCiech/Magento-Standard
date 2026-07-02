<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Model;

use Magento\Catalog\Model\Category;
use Magento\Framework\View\Result\Page;

class CategoryLayoutHandleResolver
{
    public function apply(Page $page, Category $category): void
    {
        $pageType = $this->resolvePageType($category);

        if (!$category->hasChildren()) {
            $parentPageType = strtok($pageType, '_');
            $page->addPageLayoutHandles(['type' => $parentPageType], null, false);
        }

        $page->addPageLayoutHandles(['type' => $pageType], null, false);

        $displayMode = is_string($category->getDisplayMode()) ? strtolower($category->getDisplayMode()) : '';
        $page->addPageLayoutHandles(['displaymode' => $displayMode], null, false);
        $page->addPageLayoutHandles(['id' => $category->getId()]);
    }

    private function resolvePageType(Category $category): string
    {
        $hasChildren = $category->hasChildren();

        if ($category->getIsAnchor()) {
            return $hasChildren ? 'layered' : 'layered_without_children';
        }

        return $hasChildren ? 'default' : 'default_without_children';
    }
}
