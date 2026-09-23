<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\ObjectManagerInterface;

class OfferRequestItemCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): OfferRequestItem
    {
        /** @var OfferRequestItem $offerRequestItem */
        $offerRequestItem = $this->objectManager->create(OfferRequestItem::class, $data);

        return $offerRequestItem;
    }
}
