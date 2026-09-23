<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\ObjectManagerInterface;

class OfferItemCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): OfferItem
    {
        /** @var OfferItem $offerItem */
        $offerItem = $this->objectManager->create(OfferItem::class, $data);

        return $offerItem;
    }
}
