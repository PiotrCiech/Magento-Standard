<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Data;

use Magento\Framework\ObjectManagerInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;

class OfferRequestItemDataCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(): OfferRequestItemDataInterface
    {
        /** @var OfferRequestItemDataInterface $offerRequestItemData */
        $offerRequestItemData = $this->objectManager->create(OfferRequestItemData::class);

        return $offerRequestItemData;
    }
}
