<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Data;

use Rokezzz\Offers\Api\Data\OfferItemDataInterface;

class OfferItemData implements OfferItemDataInterface
{
    private ?int $requestItemId = null;
    private int $productId = 0;
    private float $offeredQty = 0.0;
    private float $offeredPrice = 0.0;

    public function getRequestItemId(): ?int
    {
        return $this->requestItemId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getOfferedQty(): float
    {
        return $this->offeredQty;
    }

    public function getOfferedPrice(): float
    {
        return $this->offeredPrice;
    }

    public function setRequestItemId(?int $requestItemId): OfferItemDataInterface
    {
        $this->requestItemId = $requestItemId;

        return $this;
    }

    public function setProductId(int $productId): OfferItemDataInterface
    {
        $this->productId = $productId;

        return $this;
    }

    public function setOfferedQty(float $offeredQty): OfferItemDataInterface
    {
        $this->offeredQty = $offeredQty;

        return $this;
    }

    public function setOfferedPrice(float $offeredPrice): OfferItemDataInterface
    {
        $this->offeredPrice = $offeredPrice;

        return $this;
    }
}
