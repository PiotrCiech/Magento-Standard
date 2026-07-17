<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use DateTimeImmutable;
use DateTimeZone;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;

class OfferStatusValidator
{
    /**
     * @var array<string, string[]>
     */
    private const OFFER_TRANSITIONS = [
        OfferInterface::STATUS_IN_REVIEW => [
            OfferInterface::STATUS_OFFERED,
            OfferInterface::STATUS_REJECTED,
        ],
        OfferInterface::STATUS_OFFERED => [
            OfferInterface::STATUS_ACCEPTED,
            OfferInterface::STATUS_REJECTED,
            OfferInterface::STATUS_EXPIRED,
        ],
    ];

    /**
     * @var array<string, string[]>
     */
    private const REQUEST_TRANSITIONS = [
        OfferRequestInterface::STATUS_NEW => [
            OfferRequestInterface::STATUS_IN_REVIEW,
            OfferRequestInterface::STATUS_REJECTED,
        ],
        OfferRequestInterface::STATUS_IN_REVIEW => [
            OfferRequestInterface::STATUS_OFFERED,
            OfferRequestInterface::STATUS_REJECTED,
        ],
        OfferRequestInterface::STATUS_OFFERED => [
            OfferRequestInterface::STATUS_ACCEPTED,
            OfferRequestInterface::STATUS_REJECTED,
            OfferRequestInterface::STATUS_EXPIRED,
        ],
    ];

    public function __construct(
        private readonly DateTime $dateTime
    ) {
    }

    public function assertValidOfferStatus(string $status): void
    {
        if (!in_array($status, $this->getOfferStatuses(), true)) {
            throw new LocalizedException(__('The offer status "%1" is invalid.', $status));
        }
    }

    public function assertValidRequestStatus(string $status): void
    {
        if (!in_array($status, $this->getRequestStatuses(), true)) {
            throw new LocalizedException(__('The offer request status "%1" is invalid.', $status));
        }
    }

    public function assertCanTransitionOffer(string $fromStatus, string $toStatus): void
    {
        $this->assertValidOfferStatus($fromStatus);
        $this->assertValidOfferStatus($toStatus);

        $allowedStatuses = self::OFFER_TRANSITIONS[$fromStatus] ?? [];

        if (!in_array($toStatus, $allowedStatuses, true)) {
            throw new LocalizedException(
                __('The offer cannot be changed from "%1" to "%2".', $fromStatus, $toStatus)
            );
        }
    }

    public function assertCanTransitionRequest(string $fromStatus, string $toStatus): void
    {
        $this->assertValidRequestStatus($fromStatus);
        $this->assertValidRequestStatus($toStatus);

        $allowedStatuses = self::REQUEST_TRANSITIONS[$fromStatus] ?? [];

        if (!in_array($toStatus, $allowedStatuses, true)) {
            throw new LocalizedException(
                __('The offer request cannot be changed from "%1" to "%2".', $fromStatus, $toStatus)
            );
        }
    }

    public function isOfferExpired(OfferInterface $offer): bool
    {
        $expiresAt = $offer->getExpiresAt();

        if (is_null($expiresAt) || trim($expiresAt) === '') {
            return false;
        }

        $now = new DateTimeImmutable(
            $this->dateTime->gmtDate('Y-m-d H:i:s'),
            new DateTimeZone('UTC')
        );
        $expirationDate = new DateTimeImmutable($expiresAt, new DateTimeZone('UTC'));

        return $now > $expirationDate;
    }

    public function assertOfferIsUsable(OfferInterface $offer): void
    {
        if ($offer->getStatus() !== OfferInterface::STATUS_OFFERED) {
            throw new LocalizedException(__('This offer is not available for use.'));
        }

        if (!is_null($offer->getUsedAt())) {
            throw new LocalizedException(__('This offer has already been used.'));
        }

        if ($this->isOfferExpired($offer)) {
            throw new LocalizedException(__('This offer has expired.'));
        }
    }

    /**
     * @return string[]
     */
    public function getOfferStatuses(): array
    {
        return [
            OfferInterface::STATUS_IN_REVIEW,
            OfferInterface::STATUS_OFFERED,
            OfferInterface::STATUS_ACCEPTED,
            OfferInterface::STATUS_REJECTED,
            OfferInterface::STATUS_EXPIRED,
        ];
    }

    /**
     * @return string[]
     */
    public function getRequestStatuses(): array
    {
        return [
            OfferRequestInterface::STATUS_NEW,
            OfferRequestInterface::STATUS_IN_REVIEW,
            OfferRequestInterface::STATUS_OFFERED,
            OfferRequestInterface::STATUS_ACCEPTED,
            OfferRequestInterface::STATUS_REJECTED,
            OfferRequestInterface::STATUS_EXPIRED,
        ];
    }
}
