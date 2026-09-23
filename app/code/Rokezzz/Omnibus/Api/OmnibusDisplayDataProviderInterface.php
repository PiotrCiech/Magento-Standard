<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Rokezzz\Omnibus\Api\Data\OmnibusDisplayDataInterface;

interface OmnibusDisplayDataProviderInterface
{
    public function getDisplayData(
        ProductInterface $product,
        ?int $storeId = null
    ): ?OmnibusDisplayDataInterface;
}
