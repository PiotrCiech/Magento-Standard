<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Data;

use Magento\Framework\ObjectManagerInterface;
use Rokezzz\Offers\Api\Data\OfferItemDataInterface;

class OfferItemDataCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(): OfferItemDataInterface
    {
        /** @var OfferItemDataInterface $offerItemData */
        $offerItemData = $this->objectManager->create(OfferItemData::class);

        return $offerItemData;
    }
}
