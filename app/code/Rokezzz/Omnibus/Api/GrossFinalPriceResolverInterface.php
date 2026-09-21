<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface GrossFinalPriceResolverInterface
{
    public function resolve(ProductInterface $product, ?int $storeId = null): float;

    public function resolveRegular(ProductInterface $product, ?int $storeId = null): float;
}
