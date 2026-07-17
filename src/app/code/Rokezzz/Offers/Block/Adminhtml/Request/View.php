<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Request;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;

class View extends Template
{
    private const REGISTRY_KEY = 'current_offer_request';

    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly OfferRequestProviderInterface $offerRequestProvider,
        private readonly OfferProviderInterface $offerProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOfferRequest(): ?OfferRequestInterface
    {
        $offerRequest = $this->registry->registry(self::REGISTRY_KEY);

        if ($offerRequest instanceof OfferRequestInterface) {
            return $offerRequest;
        }

        $requestId = (int) $this->getRequest()->getParam('entity_id');
        $offerRequest = $this->offerRequestProvider->getById($requestId);

        if (!is_null($offerRequest)) {
            $this->registry->register(self::REGISTRY_KEY, $offerRequest);
        }

        return $offerRequest;
    }

    /**
     * @return OfferRequestItemInterface[]
     */
    public function getRequestItems(): array
    {
        $offerRequest = $this->getOfferRequest();

        if (is_null($offerRequest) || is_null($offerRequest->getEntityId())) {
            return [];
        }

        return $this->offerRequestProvider->getItemsByRequestId((int) $offerRequest->getEntityId());
    }

    public function getExistingOffer(): ?OfferInterface
    {
        $offerRequest = $this->getOfferRequest();

        if (is_null($offerRequest) || is_null($offerRequest->getEntityId())) {
            return null;
        }

        return $this->offerProvider->getByRequestId((int) $offerRequest->getEntityId());
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('rokezzz_offers/request/index');
    }

    public function getOfferEditUrl(): string
    {
        $existingOffer = $this->getExistingOffer();
        $offerRequest = $this->getOfferRequest();

        if ($existingOffer instanceof OfferInterface && !is_null($existingOffer->getEntityId())) {
            return $this->getUrl('rokezzz_offers/offer/edit', ['entity_id' => $existingOffer->getEntityId()]);
        }

        if ($offerRequest instanceof OfferRequestInterface && !is_null($offerRequest->getEntityId())) {
            return $this->getUrl('rokezzz_offers/offer/edit', ['request_id' => $offerRequest->getEntityId()]);
        }

        return $this->getUrl('rokezzz_offers/offer/index');
    }

    public function getOfferButtonLabel(): string
    {
        return $this->getExistingOffer() instanceof OfferInterface
            ? (string) __('Edit Offer')
            : (string) __('Create Offer');
    }
}
