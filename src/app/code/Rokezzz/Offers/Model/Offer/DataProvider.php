<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Offer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Model\OfferCollectionCreator;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        OfferCollectionCreator $offerCollectionCreator,
        private readonly RequestInterface $request,
        private readonly OfferProviderInterface $offerProvider,
        private readonly OfferRequestProviderInterface $offerRequestProvider,
        private readonly ProductRepositoryInterface $productRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $offerCollectionCreator->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta(): array
    {
        $meta = parent::getMeta();
        $meta['general']['children']['increment_id']['arguments']['data']['config']['visible'] =
            $this->isExistingOffer();

        return $meta;
    }

    public function getData(): array
    {
        if (!is_null($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $entityId = (int) $this->request->getParam($this->getRequestFieldName());
        $requestId = (int) $this->request->getParam('request_id');

        if ($entityId > 0) {
            $offer = $this->offerProvider->getById($entityId);

            if (!is_null($offer)) {
                $this->loadedData[$entityId] = $this->buildOfferData($offer);
            }
        } elseif ($requestId > 0) {
            $offer = $this->offerProvider->getByRequestId($requestId);

            if (!is_null($offer)) {
                $offerEntityId = $offer->getEntityId();

                if (!is_null($offerEntityId)) {
                    $this->loadedData[$offerEntityId] = $this->buildOfferData($offer);
                }
            } else {
                $this->loadedData[''] = $this->buildOfferDataFromRequest($requestId);
            }
        }

        return $this->loadedData;
    }

    private function isExistingOffer(): bool
    {
        $entityId = (int) $this->request->getParam($this->getRequestFieldName());

        if ($entityId > 0) {
            return true;
        }

        $requestId = (int) $this->request->getParam('request_id');

        if ($requestId <= 0) {
            return false;
        }

        $offer = $this->offerProvider->getByRequestId($requestId);

        return !is_null($offer) && !is_null($offer->getEntityId());
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOfferData(OfferInterface $offer): array
    {
        $data = [
            'entity_id' => $offer->getEntityId(),
            'request_id' => $offer->getRequestId(),
            'customer_id' => $offer->getCustomerId(),
            'status' => $offer->getStatus(),
            'increment_id' => $offer->getIncrementId(),
            'customer_note' => $offer->getCustomerNote(),
            'admin_note' => $offer->getAdminNote(),
            'expires_at' => $offer->getExpiresAt(),
            'items' => [],
        ];

        foreach ($this->offerProvider->getItemsByOfferId((int) $offer->getEntityId()) as $offerItem) {
            $data['items'][] = [
                'request_item_id' => $offerItem->getRequestItemId(),
                'product_id' => $offerItem->getProductId(),
                'sku' => $offerItem->getSku(),
                'name' => $offerItem->getName(),
                'requested_qty' => $offerItem->getRequestedQty(),
                'offered_qty' => $offerItem->getOfferedQty(),
                'offered_price' => $offerItem->getOfferedPrice(),
            ];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOfferDataFromRequest(int $requestId): array
    {
        $offerRequest = $this->offerRequestProvider->getById($requestId);
        $items = [];

        if (!is_null($offerRequest)) {
            foreach ($this->offerRequestProvider->getItemsByRequestId($requestId) as $requestItem) {
                $basePrice = 0.0;

                try {
                    $product = $this->productRepository->getById($requestItem->getProductId());
                    $basePrice = (float) $product->getPrice();
                } catch (NoSuchEntityException) {
                    $basePrice = 0.0;
                }

                $items[] = [
                    'request_item_id' => $requestItem->getEntityId(),
                    'product_id' => $requestItem->getProductId(),
                    'sku' => $requestItem->getSku(),
                    'name' => $requestItem->getName(),
                    'requested_qty' => $requestItem->getQty(),
                    'offered_qty' => $requestItem->getQty(),
                    'offered_price' => $basePrice,
                ];
            }
        }

        return [
            'entity_id' => null,
            'request_id' => $requestId,
            'customer_id' => $offerRequest?->getCustomerId(),
            'status' => OfferInterface::STATUS_IN_REVIEW,
            'customer_note' => $offerRequest?->getCustomerNote() ?? '',
            'admin_note' => '',
            'expires_at' => null,
            'items' => $items,
        ];
    }
}
