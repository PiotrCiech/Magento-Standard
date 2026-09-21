<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\ObjectManagerInterface;

class OfferCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): Offer
    {
        /** @var Offer $offer */
        $offer = $this->objectManager->create(Offer::class, $data);

        return $offer;
    }
}
