<?php

declare(strict_types=1);

namespace Rokezzz\Category\ViewModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Category\Model\Bestsellers\TopProductIdsProvider;
use Rokezzz\Category\Model\Config;
use Rokezzz\Category\Model\Newest\NewsDateFilter;

final class NewLabel implements ArgumentInterface
{
    public function __construct(
        private readonly NewsDateFilter $newsDateFilter,
        private readonly TopProductIdsProvider $topProductIdsProvider,
        private readonly Config $config
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

    public function isBestseller(ProductInterface $product): bool
    {
        if (!$this->config->isBestsellerBadgeEnabled()) {
            return false;
        }

        return in_array((int) $product->getId(), $this->topProductIdsProvider->getTopProductIds(), true);
    }

    /**
     * @return array{css: string, text: string}|null
     */
    public function getBadge(ProductInterface $product): ?array
    {
        if ($this->isBestseller($product)) {
            return ['css' => 'product-label--bestseller', 'text' => (string) __('Bestseller')];
        }

        if ($this->isNew($product)) {
            return ['css' => 'product-label--new', 'text' => (string) __('New')];
        }

        return null;
    }

    public function getDiscountLabel(ProductInterface $product): ?string
    {
        if (!$this->config->isDiscountBadgeEnabled()) {
            return null;
        }

        $regular = (float) $product->getPrice();
        $final = (float) $product->getFinalPrice();

        if ($regular <= 0.0 || $final <= 0.0 || $final >= $regular) {
            return null;
        }

        $percent = (int) round((1 - $final / $regular) * 100);

        return $percent >= 1 ? '-' . $percent . '%' : null;
    }
}
