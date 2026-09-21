<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class OfferExpirationManager
{
    public function __construct(
        private readonly OfferCollectionCreator $offerCollectionCreator,
        private readonly OfferRequestCreator $offerRequestCreator,
        private readonly OfferResource $offerResource,
        private readonly OfferRequestResource $offerRequestResource,
        private readonly OfferStatusValidator $offerStatusValidator,
        private readonly DateTime $dateTime
    ) {
    }

    public function expireDueOffers(): int
    {
        $expiredCount = 0;
        $now = $this->dateTime->gmtDate('Y-m-d H:i:s');
        $collection = $this->offerCollectionCreator->create();
        $collection->addFieldToFilter('status', OfferInterface::STATUS_OFFERED);
        $collection->addFieldToFilter('expires_at', ['notnull' => true]);
        $collection->addFieldToFilter('expires_at', ['neq' => '']);
        $collection->addFieldToFilter('expires_at', ['lteq' => $now]);

        foreach ($collection as $offer) {
            if (!$offer instanceof Offer || is_null($offer->getEntityId())) {
                continue;
            }

            if (!$this->offerStatusValidator->isOfferExpired($offer)) {
                continue;
            }

            $this->expireOffer($offer);
            ++$expiredCount;
        }

        return $expiredCount;
    }

    /**
     * @throws LocalizedException
     */
    private function expireOffer(Offer $offer): void
    {
        $this->offerStatusValidator->assertCanTransitionOffer(
            $offer->getStatus(),
            OfferInterface::STATUS_EXPIRED
        );

        $offer->setStatus(OfferInterface::STATUS_EXPIRED);
        $this->offerResource->save($offer);
        $this->expireLinkedRequest((int) $offer->getRequestId());
    }

    /**
     * @throws LocalizedException
     */
    private function expireLinkedRequest(int $requestId): void
    {
        $offerRequest = $this->loadOfferRequest($requestId);

        if (!$this->canExpireOfferRequest($offerRequest)) {
            return;
        }

        $this->offerStatusValidator->assertCanTransitionRequest(
            $offerRequest->getStatus(),
            OfferRequestInterface::STATUS_EXPIRED
        );
        $offerRequest->setStatus(OfferRequestInterface::STATUS_EXPIRED);
        $this->offerRequestResource->save($offerRequest);
    }

    private function loadOfferRequest(int $requestId): OfferRequest
    {
        $offerRequest = $this->offerRequestCreator->create();
        $this->offerRequestResource->load($offerRequest, $requestId);

        return $offerRequest;
    }

    private function canExpireOfferRequest(OfferRequest $offerRequest): bool
    {
        if (is_null($offerRequest->getEntityId())) {
            return false;
        }

        if ($offerRequest->getStatus() === OfferRequestInterface::STATUS_EXPIRED) {
            return false;
        }

        return $offerRequest->getStatus() === OfferRequestInterface::STATUS_OFFERED;
    }
}
