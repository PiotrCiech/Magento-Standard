<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Offers\Api\Data\OfferRequestItemInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestItemResource;

class OfferRequestItem extends AbstractModel implements OfferRequestItemInterface
{
    protected function _construct(): void
    {
        $this->_init(OfferRequestItemResource::class);
    }

    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);

        return is_null($entityId) ? null : (int) $entityId;
    }

    public function getRequestId(): int
    {
        return (int) $this->getData(self::REQUEST_ID);
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

    public function getQty(): float
    {
        return (float) $this->getData(self::QTY);
    }

    public function getCustomerNote(): string
    {
        return trim((string) $this->getData(self::CUSTOMER_NOTE));
    }

    public function setRequestId(int $requestId): OfferRequestItemInterface
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    public function setProductId(int $productId): OfferRequestItemInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function setSku(string $sku): OfferRequestItemInterface
    {
        return $this->setData(self::SKU, trim($sku));
    }

    public function setName(string $name): OfferRequestItemInterface
    {
        return $this->setData(self::NAME, trim($name));
    }

    public function setQty(float $qty): OfferRequestItemInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    public function setCustomerNote(string $customerNote): OfferRequestItemInterface
    {
        return $this->setData(self::CUSTOMER_NOTE, trim($customerNote));
    }
}
