<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api\Data;

interface OfferRequestItemDataInterface
{
    public function getProductId(): int;

    public function getQty(): float;

    public function getCustomerNote(): string;

    public function setProductId(int $productId): self;

    public function setQty(float $qty): self;

    public function setCustomerNote(string $customerNote): self;
}
