<?php

declare(strict_types=1);

namespace Rokezzz\DynamicCategories\ViewModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\DynamicCategories\Model\Newest\NewsDateFilter;

final class NewLabel implements ArgumentInterface
{
    public function __construct(
        private readonly NewsDateFilter $newsDateFilter
    ) {
    }

    public function isNew(ProductInterface $product): bool
    {
        $from = $product->getNewsFromDate();
        $to = $product->getNewsToDate();

        return $this->newsDateFilter->isCurrentlyNew(
            is_string($from) ? $from : null,
            is_string($to) ? $to : null
        );
    }
}
