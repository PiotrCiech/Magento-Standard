<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferItemDataInterface
{
    public function getRequestItemId(): ?int;

    public function getProductId(): int;

    public function getOfferedQty(): float;

    public function getOfferedPrice(): float;

    public function setRequestItemId(?int $requestItemId): self;

    public function setProductId(int $productId): self;

    public function setOfferedQty(float $offeredQty): self;

    public function setOfferedPrice(float $offeredPrice): self;
}
