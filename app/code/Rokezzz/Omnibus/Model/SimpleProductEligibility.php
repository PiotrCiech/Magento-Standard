<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;

final class SimpleProductEligibility
{
    public function isEligible(ProductInterface $product): bool
    {
        if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
            return false;
        }

        return (int) $product->getId() > 0;
    }
}
