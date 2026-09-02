<?php

declare(strict_types=1);

namespace Rokezzz\Category\ViewModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Category\Model\Config;

final class StockLabel implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly StockRegistryInterface $stockRegistry
    ) {
    }
    public function getLabel(ProductInterface $product): ?string
    {
        if (!$this->config->isLowStockBadgeEnabled()) {
            return null;
        }

        $productId = (int) $product->getId();
        if ($productId <= 0) {
            return null;
        }

        $stockItem = $this->stockRegistry->getStockItem($productId);
        if (!$stockItem->getManageStock()) {
            return null;
        }

        $qty = (float) $stockItem->getQty();
        if ($qty <= 0 || $qty > $this->config->getLowStockThreshold()) {
            return null;
        }

        return $qty <= 1
            ? (string) __('Last item in stock')
            : (string) __('Last %1 in stock', (int) $qty);
    }
}
