<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface PriceHistoryRecorderInterface
{
    public function recordForAllStores(ProductInterface $product): void;

    public function recordForProduct(int $productId, int $storeId): void;
}
