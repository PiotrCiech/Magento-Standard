<?php

declare(strict_types=1);

namespace Rokezzz\Offers\ViewModel\Account;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Model\Frontend\StatusLabelProvider;

class RequestHistory implements ArgumentInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly OfferRequestProviderInterface $offerRequestProvider,
        private readonly OfferProviderInterface $offerProvider,
        private readonly StatusLabelProvider $statusLabelProvider,
        private readonly UrlInterface $url,
        private readonly TimezoneInterface $timezone
    ) {
    }

    /**
     * @return OfferRequestInterface[]
     */
    public function getRequests(): array
    {
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($customerId === 0) {
            return [];
        }

        return $this->offerRequestProvider->getListByCustomerId($customerId);
    }

    public function getItemCount(OfferRequestInterface $offerRequest): int
    {
        if (is_null($offerRequest->getEntityId())) {
            return 0;
        }

        return count($this->offerRequestProvider->getItemsByRequestId((int) $offerRequest->getEntityId()));
    }

    public function getOfferViewUrl(OfferRequestInterface $offerRequest): ?string
    {
        if (is_null($offerRequest->getEntityId())) {
            return null;
        }

        $offer = $this->offerProvider->getByRequestId((int) $offerRequest->getEntityId());

        if (is_null($offer) || is_null($offer->getEntityId())) {
            return null;
        }

        return $this->url->getUrl('rokezzz_offers/offer/view', [
            'entity_id' => $offer->getEntityId(),
        ]);
    }

    public function formatDate(string $date): string
    {
        if (trim($date) === '') {
            return '';
        }

        return (string) $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, true);
    }

    public function getStatusLabel(OfferRequestInterface $offerRequest): string
    {
        return $this->statusLabelProvider->getRequestStatusLabel($offerRequest->getStatus());
    }

    public function getStatusClass(OfferRequestInterface $offerRequest): string
    {
        return $this->statusLabelProvider->getRequestStatusClass($offerRequest->getStatus());
    }
}
