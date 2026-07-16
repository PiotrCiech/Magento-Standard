<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Data;

use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;

class OfferRequestItemData implements OfferRequestItemDataInterface
{
    private int $productId = 0;
    private float $qty = 0.0;

    private string $customerNote = '';

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQty(): float
    {
        return $this->qty;
    }

    public function getCustomerNote(): string
    {
        return $this->customerNote;
    }

    public function setProductId(int $productId): OfferRequestItemDataInterface
    {
        $this->productId = $productId;

        return $this;
    }

    public function setQty(float $qty): OfferRequestItemDataInterface
    {
        $this->qty = $qty;

        return $this;
    }

    public function setCustomerNote(string $customerNote): OfferRequestItemDataInterface
    {
        $this->customerNote = trim($customerNote);

        return $this;
    }
}
