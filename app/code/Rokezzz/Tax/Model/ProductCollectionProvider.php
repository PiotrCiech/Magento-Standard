<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;

final class ProductCollectionProvider
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(): Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
