<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;
use Rokezzz\Offers\Model\ResourceModel\OfferItem\CollectionFactory as OfferItemCollectionFactory;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class OfferProvider implements OfferProviderInterface
{
    public function __construct(
        private readonly OfferCreator $offerCreator,
        private readonly OfferResource $offerResource,
        private readonly OfferCollectionFactory $offerCollectionFactory,
        private readonly OfferItemCollectionFactory $offerItemCollectionFactory
    ) {
    }

    public function getById(int $offerId): ?OfferInterface
    {
        $offer = $this->offerCreator->create();
        $this->offerResource->load($offer, $offerId);

        if (is_null($offer->getEntityId())) {
            return null;
        }

        return $offer;
    }

    public function getByIdForCustomer(int $offerId, int $customerId): OfferInterface
    {
        $offer = $this->getById($offerId);

        if (is_null($offer)) {
            throw new NoSuchEntityException(__('The offer no longer exists.'));
        }

        if ($offer->getCustomerId() !== $customerId) {
            throw new NoSuchEntityException(__('The offer no longer exists.'));
        }

        return $offer;
    }

    public function getListByCustomerId(int $customerId): array
    {
        $collection = $this->offerCollectionFactory->create();
        $collection->addFieldToFilter(OfferInterface::CUSTOMER_ID, $customerId);
        $collection->setOrder(OfferInterface::CREATED_AT, 'DESC');

        return array_values($collection->getItems());
    }

    public function getItemsByOfferId(int $offerId): array
    {
        $collection = $this->offerItemCollectionFactory->create();
        $collection->addFieldToFilter(OfferItemInterface::OFFER_ID, $offerId);

        return array_values($collection->getItems());
    }
}
