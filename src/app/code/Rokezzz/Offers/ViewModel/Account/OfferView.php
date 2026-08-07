<?php

declare(strict_types=1);

namespace Rokezzz\Offers\ViewModel\Account;

use Throwable;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Model\Frontend\StatusLabelProvider;
use Rokezzz\Offers\Model\OfferStatusValidator;

class OfferView implements ArgumentInterface
{
    private ?OfferInterface $offer = null;

    private bool $offerResolved = false;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly CustomerSession $customerSession,
        private readonly OfferProviderInterface $offerProvider,
        private readonly OfferStatusValidator $offerStatusValidator,
        private readonly StatusLabelProvider $statusLabelProvider,
        private readonly UrlInterface $url,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly TimezoneInterface $timezone
    ) {
    }

    public function getOffer(): ?OfferInterface
    {
        if ($this->offerResolved) {
            return $this->offer;
        }

        $this->offerResolved = true;
        $customerId = (int) $this->customerSession->getCustomerId();
        $offerId = (int) $this->request->getParam('entity_id');

        if ($customerId === 0 || $offerId === 0) {
            return null;
        }

        try {
            $this->offer = $this->offerProvider->getByIdForCustomer($offerId, $customerId);
        } catch (NoSuchEntityException) {
            $this->offer = null;
        }

        return $this->offer;
    }

    /**
     * @return OfferItemInterface[]
     */
    public function getItems(): array
    {
        $offer = $this->getOffer();

        if (is_null($offer) || is_null($offer->getEntityId())) {
            return [];
        }

        return $this->offerProvider->getItemsByOfferId((int) $offer->getEntityId());
    }

    public function canApply(): bool
    {
        $offer = $this->getOffer();

        if (is_null($offer)) {
            return false;
        }

        try {
            $this->offerStatusValidator->assertOfferIsUsable($offer);
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    public function getApplyUrl(): string
    {
        $offer = $this->getOffer();

        if (is_null($offer) || is_null($offer->getEntityId())) {
            return '';
        }

        return $this->url->getUrl('rokezzz_offers/offer/apply', [
            'entity_id' => $offer->getEntityId(),
        ]);
    }

    public function getBackUrl(): string
    {
        return $this->url->getUrl('rokezzz_offers/offer/index');
    }

    public function formatPrice(float $amount): string
    {
        return $this->priceCurrency->convertAndFormat($amount, false);
    }

    public function formatDate(?string $date): string
    {
        if (is_null($date) || trim($date) === '') {
            return '';
        }

        return (string) $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, true);
    }

    public function getExpirationLabel(): string
    {
        $offer = $this->getOffer();

        if (is_null($offer)) {
            return '';
        }

        $expiresAt = $offer->getExpiresAt();

        if (is_null($expiresAt) || trim($expiresAt) === '') {
            return (string) __('Available indefinitely');
        }

        return $this->formatDate($expiresAt);
    }

    public function getStatusLabel(): string
    {
        $offer = $this->getOffer();

        if (is_null($offer)) {
            return '';
        }

        return $this->statusLabelProvider->getOfferStatusLabel($offer->getStatus());
    }

    public function getStatusClass(): string
    {
        $offer = $this->getOffer();

        if (is_null($offer)) {
            return 'rokezzz-status--default';
        }

        return $this->statusLabelProvider->getOfferStatusClass($offer->getStatus());
    }
}
