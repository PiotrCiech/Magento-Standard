<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestItem\CollectionFactory as OfferRequestItemCollectionFactory;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;

class OfferRequestProvider implements OfferRequestProviderInterface
{
    public function __construct(
        private readonly OfferRequestCreator $offerRequestCreator,
        private readonly OfferRequestResource $offerRequestResource,
        private readonly OfferRequestItemCollectionFactory $offerRequestItemCollectionFactory
    ) {
    }

    public function getById(int $requestId): ?OfferRequestInterface
    {
        $offerRequest = $this->offerRequestCreator->create();
        $this->offerRequestResource->load($offerRequest, $requestId);

        if (is_null($offerRequest->getEntityId())) {
            return null;
        }

        return $offerRequest;
    }

    public function getByIdForCustomer(int $requestId, int $customerId): OfferRequestInterface
    {
        $offerRequest = $this->getById($requestId);

        if (is_null($offerRequest)) {
            throw new NoSuchEntityException(__('The offer request no longer exists.'));
        }

        if ($offerRequest->getCustomerId() !== $customerId) {
            throw new NoSuchEntityException(__('The offer request no longer exists.'));
        }

        return $offerRequest;
    }

    public function getItemsByRequestId(int $requestId): array
    {
        $collection = $this->offerRequestItemCollectionFactory->create();
        $collection->addFieldToFilter(OfferRequestItemInterface::REQUEST_ID, $requestId);

        return array_values($collection->getItems());
    }
}
