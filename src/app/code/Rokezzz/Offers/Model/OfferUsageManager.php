<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class OfferUsageManager
{
    public function __construct(
        private readonly OfferCreator $offerCreator,
        private readonly OfferRequestCreator $offerRequestCreator,
        private readonly OfferResource $offerResource,
        private readonly OfferRequestResource $offerRequestResource,
        private readonly OfferStatusValidator $offerStatusValidator
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function markAsUsed(int $offerId): void
    {
        $offer = $this->loadOffer($offerId);

        if (!$this->canMarkOfferAsUsed($offer)) {
            return;
        }

        $this->offerStatusValidator->assertCanTransitionOffer(
            $offer->getStatus(),
            OfferInterface::STATUS_ACCEPTED
        );

        $this->markOfferAccepted($offer);
        $this->offerResource->save($offer);
        $this->acceptOfferRequest((int) $offer->getRequestId());
    }

    private function loadOffer(int $offerId): Offer
    {
        $offer = $this->offerCreator->create();
        $this->offerResource->load($offer, $offerId);

        return $offer;
    }

    private function loadOfferRequest(int $requestId): OfferRequest
    {
        $offerRequest = $this->offerRequestCreator->create();
        $this->offerRequestResource->load($offerRequest, $requestId);

        return $offerRequest;
    }

    private function canMarkOfferAsUsed(Offer $offer): bool
    {
        return !is_null($offer->getEntityId())
            && $offer->getStatus() === OfferInterface::STATUS_OFFERED
            && is_null($offer->getUsedAt());
    }

    private function canAcceptOfferRequest(OfferRequest $offerRequest): bool
    {
        return !is_null($offerRequest->getEntityId())
            && $offerRequest->getStatus() !== OfferRequestInterface::STATUS_ACCEPTED;
    }

    private function markOfferAccepted(Offer $offer): void
    {
        $now = date('Y-m-d H:i:s');
        $offer->setStatus(OfferInterface::STATUS_ACCEPTED);
        $offer->setAcceptedAt($now);
        $offer->setUsedAt($now);
    }

    /**
     * @throws LocalizedException
     */
    private function acceptOfferRequest(int $requestId): void
    {
        $offerRequest = $this->loadOfferRequest($requestId);

        if (!$this->canAcceptOfferRequest($offerRequest)) {
            return;
        }

        $this->offerStatusValidator->assertCanTransitionRequest(
            $offerRequest->getStatus(),
            OfferRequestInterface::STATUS_ACCEPTED
        );
        $offerRequest->setStatus(OfferRequestInterface::STATUS_ACCEPTED);
        $this->offerRequestResource->save($offerRequest);
    }
}
