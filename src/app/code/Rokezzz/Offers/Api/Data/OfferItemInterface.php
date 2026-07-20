<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferItemInterface
{
    public const ENTITY_ID = 'entity_id';
    public const OFFER_ID = 'offer_id';
    public const REQUEST_ITEM_ID = 'request_item_id';
    public const PRODUCT_ID = 'product_id';
    public const SKU = 'sku';
    public const NAME = 'name';
    public const REQUESTED_QTY = 'requested_qty';
    public const OFFERED_QTY = 'offered_qty';
    public const BASE_PRICE = 'base_price';
    public const OFFERED_PRICE = 'offered_price';

    public function getEntityId(): ?int;

    public function getOfferId(): int;

    public function getRequestItemId(): ?int;

    public function getProductId(): int;

    public function getSku(): string;

    public function getName(): string;

    public function getRequestedQty(): float;

    public function getOfferedQty(): float;

    public function getBasePrice(): float;

    public function getOfferedPrice(): float;

    public function setOfferId(int $offerId): self;

    public function setRequestItemId(?int $requestItemId): self;

    public function setProductId(int $productId): self;

    public function setSku(string $sku): self;

    public function setName(string $name): self;

    public function setRequestedQty(float $requestedQty): self;

    public function setOfferedQty(float $offeredQty): self;

    public function setBasePrice(float $basePrice): self;

    public function setOfferedPrice(float $offeredPrice): self;
}
