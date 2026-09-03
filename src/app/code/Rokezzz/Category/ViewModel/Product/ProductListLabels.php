<?php

declare(strict_types=1);

namespace Rokezzz\Category\ViewModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

final class ProductListLabels implements ArgumentInterface
{
    public function __construct(
        private readonly NewLabel $newLabel
    ) {
    }

    /**
     * @return array{badge: array{css: string, text: string}|null, discountLabel: string|null}
     */
    public function forProduct(ProductInterface $product): array
    {
        return [
            'badge' => $this->newLabel->getBadge($product),
            'discountLabel' => $this->newLabel->getDiscountLabel($product),
        ];
    }
}
