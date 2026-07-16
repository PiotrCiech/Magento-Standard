<?php

declare(strict_types=1);

namespace Rokezzz\Offers\ViewModel\Account;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Model\Frontend\StatusLabelProvider;
use Rokezzz\Offers\Model\OfferStatusValidator;

class OfferList implements ArgumentInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly OfferProviderInterface $offerProvider,
        private readonly OfferStatusValidator $offerStatusValidator,
        private readonly StatusLabelProvider $statusLabelProvider,
        private readonly UrlInterface $url,
        private readonly TimezoneInterface $timezone
    ) {
    }

    /**
     * @return OfferInterface[]
     */
    public function getOffers(): array
    {
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($customerId === 0) {
            return [];
        }

        return $this->offerProvider->getListByCustomerId($customerId);
    }

    public function getViewUrl(OfferInterface $offer): string
    {
        return $this->url->getUrl('rokezzz_offers/offer/view', [
            'entity_id' => $offer->getEntityId(),
        ]);
    }

    public function getApplyUrl(OfferInterface $offer): string
    {
        return $this->url->getUrl('rokezzz_offers/offer/apply', [
            'entity_id' => $offer->getEntityId(),
        ]);
    }

    public function getStatusLabel(OfferInterface $offer): string
    {
        return $this->statusLabelProvider->getOfferStatusLabel($offer->getStatus());
    }

    public function getStatusClass(OfferInterface $offer): string
    {
        return $this->statusLabelProvider->getOfferStatusClass($offer->getStatus());
    }

    public function getExpirationLabel(OfferInterface $offer): string
    {
        $expiresAt = $offer->getExpiresAt();

        if (is_null($expiresAt) || trim($expiresAt) === '') {
            return (string) __('Available indefinitely');
        }

        return $this->formatDate($expiresAt);
    }

    public function formatDate(?string $date): string
    {
        if (is_null($date) || trim($date) === '') {
            return '';
        }

        return (string) $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, true);
    }

    public function canApply(OfferInterface $offer): bool
    {
        try {
            $this->offerStatusValidator->assertOfferIsUsable($offer);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }
}
