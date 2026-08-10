<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Offers\Api\Data\OfferItemInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferItemResource;

class OfferItem extends AbstractModel implements OfferItemInterface
{
    protected function _construct(): void
    {
        $this->_init(OfferItemResource::class);
    }

    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);

        return is_null($entityId) ? null : (int) $entityId;
    }

    public function getOfferId(): int
    {
        return (int) $this->getData(self::OFFER_ID);
    }

    public function getRequestItemId(): ?int
    {
        $requestItemId = $this->getData(self::REQUEST_ITEM_ID);

        return is_null($requestItemId) ? null : (int) $requestItemId;
    }

    public function getProductId(): int
    {
        return (int) $this->getData(self::PRODUCT_ID);
    }

    public function getSku(): string
    {
        return trim((string) $this->getData(self::SKU));
    }

    public function getName(): string
    {
        return trim((string) $this->getData(self::NAME));
    }

    public function getRequestedQty(): float
    {
        return (float) $this->getData(self::REQUESTED_QTY);
    }

    public function getOfferedQty(): float
    {
        return (float) $this->getData(self::OFFERED_QTY);
    }

    public function getBasePrice(): float
    {
        return (float) $this->getData(self::BASE_PRICE);
    }

    public function getOfferedPrice(): float
    {
        return (float) $this->getData(self::OFFERED_PRICE);
    }

    public function setOfferId(int $offerId): OfferItemInterface
    {
        return $this->setData(self::OFFER_ID, $offerId);
    }

    public function setRequestItemId(?int $requestItemId): OfferItemInterface
    {
        return $this->setData(self::REQUEST_ITEM_ID, $requestItemId);
    }

    public function setProductId(int $productId): OfferItemInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function setSku(string $sku): OfferItemInterface
    {
        return $this->setData(self::SKU, trim($sku));
    }

    public function setName(string $name): OfferItemInterface
    {
        return $this->setData(self::NAME, trim($name));
    }

    public function setRequestedQty(float $requestedQty): OfferItemInterface
    {
        return $this->setData(self::REQUESTED_QTY, $requestedQty);
    }

    public function setOfferedQty(float $offeredQty): OfferItemInterface
    {
        return $this->setData(self::OFFERED_QTY, $offeredQty);
    }

    public function setBasePrice(float $basePrice): OfferItemInterface
    {
        return $this->setData(self::BASE_PRICE, $basePrice);
    }

    public function setOfferedPrice(float $offeredPrice): OfferItemInterface
    {
        return $this->setData(self::OFFERED_PRICE, $offeredPrice);
    }
}
