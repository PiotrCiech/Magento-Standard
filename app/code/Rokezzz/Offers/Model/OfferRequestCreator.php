<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\ObjectManagerInterface;

class OfferRequestCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): OfferRequest
    {
        /** @var OfferRequest $offerRequest */
        $offerRequest = $this->objectManager->create(OfferRequest::class, $data);

        return $offerRequest;
    }
}
