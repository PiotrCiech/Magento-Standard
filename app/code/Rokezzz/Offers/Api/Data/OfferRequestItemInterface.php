<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferRequestItemInterface
{
    public const ENTITY_ID = 'entity_id';
    public const REQUEST_ID = 'request_id';
    public const PRODUCT_ID = 'product_id';
    public const SKU = 'sku';
    public const NAME = 'name';
    public const QTY = 'qty';
    public const CUSTOMER_NOTE = 'customer_note';

    public function getEntityId(): ?int;

    public function getRequestId(): int;

    public function getProductId(): int;

    public function getSku(): string;

    public function getName(): string;

    public function getQty(): float;

    public function getCustomerNote(): string;

    public function setRequestId(int $requestId): self;

    public function setProductId(int $productId): self;

    public function setSku(string $sku): self;

    public function setName(string $name): self;

    public function setQty(float $qty): self;

    public function setCustomerNote(string $customerNote): self;
}
