<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemDataInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemInterface;
use Rokezzz\Offers\Api\OfferManagementInterface;
use Rokezzz\Offers\Api\OfferNotificationSenderInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferItemResource;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class OfferManagement implements OfferManagementInterface
{
    public function __construct(
        private readonly OfferCreator $offerCreator,
        private readonly OfferItemCreator $offerItemCreator,
        private readonly OfferRequestCreator $offerRequestCreator,
        private readonly OfferResource $offerResource,
        private readonly OfferItemResource $offerItemResource,
        private readonly OfferRequestResource $offerRequestResource,
        private readonly OfferRequestProviderInterface $offerRequestProvider,
        private readonly OfferProviderInterface $offerProvider,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly OfferStatusValidator $offerStatusValidator,
        private readonly OfferIncrementIdGenerator $offerIncrementIdGenerator,
        private readonly OfferAccessTokenGenerator $offerAccessTokenGenerator,
        private readonly OfferNotificationSenderInterface $offerNotificationSender
    ) {
    }

    /**
     * @param OfferItemDataInterface[] $items
     */
    public function createFromRequest(
        int $requestId,
        string $adminNote,
        ?string $expiresAt,
        array $items
    ): OfferInterface {
        $offerRequest = $this->loadOfferRequestOrThrow($requestId);
        $this->offerStatusValidator->assertCanTransitionRequest(
            $offerRequest->getStatus(),
            OfferRequestInterface::STATUS_IN_REVIEW
        );
        $this->assertOfferItemsAreValid($items);

        $offer = $this->offerCreator->create();
        $offer->setRequestId($requestId);
        $offer->setCustomerId($offerRequest->getCustomerId());
        $offer->setStatus(OfferInterface::STATUS_IN_REVIEW);
        $offer->setIncrementId($this->offerIncrementIdGenerator->generate());
        $offer->setAccessToken($this->offerAccessTokenGenerator->generate());
        $offer->setCustomerNote($offerRequest->getCustomerNote());
        $offer->setAdminNote($adminNote);
        $offer->setExpiresAt($expiresAt);

        $this->offerResource->save($offer);
        $this->replaceOfferItems($offer, $items, $offerRequest);
        $this->updateRequestStatus($offerRequest, OfferRequestInterface::STATUS_IN_REVIEW);

        return $offer;
    }

    /**
     * @param OfferItemDataInterface[] $items
     */
    public function update(
        int $offerId,
        string $adminNote,
        ?string $expiresAt,
        array $items
    ): OfferInterface {
        $offer = $this->loadOfferOrThrow($offerId);

        if ($offer->getStatus() !== OfferInterface::STATUS_IN_REVIEW) {
            throw new LocalizedException(__('Only offers in review can be updated.'));
        }

        $offerRequest = $this->loadOfferRequestOrThrow($offer->getRequestId());
        $this->assertOfferItemsAreValid($items);

        $offer->setAdminNote($adminNote);
        $offer->setExpiresAt($expiresAt);

        $this->offerResource->save($offer);
        $this->deleteExistingOfferItems($offerId);
        $this->replaceOfferItems($offer, $items, $offerRequest);

        return $offer;
    }

    public function markAsOffered(int $offerId): OfferInterface
    {
        $offer = $this->loadOfferOrThrow($offerId);
        $offerRequest = $this->loadOfferRequestOrThrow($offer->getRequestId());

        $this->offerStatusValidator->assertCanTransitionOffer(
            $offer->getStatus(),
            OfferInterface::STATUS_OFFERED
        );
        $this->assertOfferHasValidItems($offerId);

        $offer->setStatus(OfferInterface::STATUS_OFFERED);
        $offer->setSentAt(date('Y-m-d H:i:s'));

        $this->offerResource->save($offer);
        $this->updateRequestStatus($offerRequest, OfferRequestInterface::STATUS_OFFERED);
        $this->offerNotificationSender->sendOfferSentNotification($offer);

        return $offer;
    }

    public function reject(int $offerId): OfferInterface
    {
        $offer = $this->loadOfferOrThrow($offerId);
        $offerRequest = $this->loadOfferRequestOrThrow($offer->getRequestId());

        $this->offerStatusValidator->assertCanTransitionOffer(
            $offer->getStatus(),
            OfferInterface::STATUS_REJECTED
        );

        $offer->setStatus(OfferInterface::STATUS_REJECTED);

        $this->offerResource->save($offer);
        $this->updateRequestStatus($offerRequest, OfferRequestInterface::STATUS_REJECTED);

        return $offer;
    }

    /**
     * @param OfferItemDataInterface[] $items
     *
     * @throws LocalizedException
     */
    private function assertOfferItemsAreValid(array $items): void
    {
        if ($items === []) {
            throw new LocalizedException(__('Please add at least one item to the offer.'));
        }

        foreach ($items as $itemData) {
            if (!$itemData instanceof OfferItemDataInterface) {
                throw new LocalizedException(__('The offer items are invalid.'));
            }

            if ($itemData->getProductId() <= 0) {
                throw new LocalizedException(__('Each offer item must include a valid product.'));
            }

            if ($itemData->getOfferedQty() <= 0) {
                throw new LocalizedException(__('Each offer item must include a quantity greater than zero.'));
            }

            if ($itemData->getOfferedPrice() < 0) {
                throw new LocalizedException(__('Each offer item must include a valid price.'));
            }
        }
    }

    /**
     * @throws LocalizedException
     */
    private function assertOfferHasValidItems(int $offerId): void
    {
        $offerItems = $this->offerProvider->getItemsByOfferId($offerId);

        if ($offerItems === []) {
            throw new LocalizedException(__('The offer must contain at least one item.'));
        }

        foreach ($offerItems as $offerItem) {
            if ($offerItem->getOfferedQty() <= 0) {
                throw new LocalizedException(__('Each offer item must include a quantity greater than zero.'));
            }

            if ($offerItem->getOfferedPrice() < 0) {
                throw new LocalizedException(__('Each offer item must include a valid price.'));
            }
        }
    }

    /**
     * @param OfferItemDataInterface[] $items
     *
     * @throws NoSuchEntityException
     */
    private function replaceOfferItems(
        Offer $offer,
        array $items,
        OfferRequest $offerRequest
    ): void {
        $requestItemsById = $this->getRequestItemsIndexedById($offerRequest);

        foreach ($items as $itemData) {
            $requestItem = $this->resolveRequestItem($itemData, $requestItemsById);
            $product = $this->productRepository->getById($itemData->getProductId());

            $offerItem = $this->offerItemCreator->create();
            $offerItem->setOfferId((int) $offer->getEntityId());
            $offerItem->setRequestItemId($requestItem?->getEntityId());
            $offerItem->setProductId($itemData->getProductId());
            $offerItem->setSku((string) $product->getSku());
            $offerItem->setName((string) $product->getName());
            $offerItem->setRequestedQty($requestItem instanceof OfferRequestItemInterface ? $requestItem->getQty() : 0.0);
            $offerItem->setOfferedQty($itemData->getOfferedQty());
            $offerItem->setBasePrice((float) $product->getPrice());
            $offerItem->setOfferedPrice($itemData->getOfferedPrice());

            $this->offerItemResource->save($offerItem);
        }
    }

    /**
     * @return array<int, OfferRequestItemInterface>
     */
    private function getRequestItemsIndexedById(OfferRequest $offerRequest): array
    {
        $indexedItems = [];

        foreach ($this->offerRequestProvider->getItemsByRequestId((int) $offerRequest->getEntityId()) as $requestItem) {
            $entityId = $requestItem->getEntityId();

            if (!is_null($entityId)) {
                $indexedItems[$entityId] = $requestItem;
            }
        }

        return $indexedItems;
    }

    /**
     * @param array<int, OfferRequestItemInterface> $requestItemsById
     */
    private function resolveRequestItem(
        OfferItemDataInterface $itemData,
        array $requestItemsById
    ): ?OfferRequestItemInterface {
        $requestItemId = $itemData->getRequestItemId();

        if (!is_null($requestItemId) && isset($requestItemsById[$requestItemId])) {
            return $requestItemsById[$requestItemId];
        }

        foreach ($requestItemsById as $requestItem) {
            if ($requestItem->getProductId() === $itemData->getProductId()) {
                return $requestItem;
            }
        }

        return null;
    }

    private function deleteExistingOfferItems(int $offerId): void
    {
        foreach ($this->offerProvider->getItemsByOfferId($offerId) as $offerItem) {
            if (!$offerItem instanceof OfferItem || is_null($offerItem->getEntityId())) {
                continue;
            }

            $this->offerItemResource->delete($offerItem);
        }
    }

    private function updateRequestStatus(OfferRequest $offerRequest, string $status): void
    {
        if ($offerRequest->getStatus() === $status) {
            return;
        }

        $this->offerStatusValidator->assertCanTransitionRequest($offerRequest->getStatus(), $status);
        $offerRequest->setStatus($status);
        $this->offerRequestResource->save($offerRequest);
    }

    /**
     * @throws NoSuchEntityException
     */
    private function loadOfferRequestOrThrow(int $requestId): OfferRequest
    {
        $offerRequest = $this->offerRequestCreator->create();
        $this->offerRequestResource->load($offerRequest, $requestId);

        if (is_null($offerRequest->getEntityId())) {
            throw new NoSuchEntityException(__('The offer request no longer exists.'));
        }

        return $offerRequest;
    }

    /**
     * @throws NoSuchEntityException
     */
    private function loadOfferOrThrow(int $offerId): Offer
    {
        $offer = $this->offerCreator->create();
        $this->offerResource->load($offer, $offerId);

        if (is_null($offer->getEntityId())) {
            throw new NoSuchEntityException(__('The offer no longer exists.'));
        }

        return $offer;
    }
}
