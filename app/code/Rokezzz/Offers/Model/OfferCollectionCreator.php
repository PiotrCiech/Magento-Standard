<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\ObjectManagerInterface;
use Rokezzz\Offers\Model\ResourceModel\Offer\Collection;

class OfferCollectionCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(): Collection
    {
        /** @var Collection $collection */
        $collection = $this->objectManager->create(Collection::class);

        return $collection;
    }
}
