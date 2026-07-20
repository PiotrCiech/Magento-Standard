<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;

class OfferRequest extends AbstractModel implements OfferRequestInterface
{
    protected function _construct(): void
    {
        $this->_init(OfferRequestResource::class);
    }

    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);

        return is_null($entityId) ? null : (int) $entityId;
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function getStatus(): string
    {
        return (string) $this->getData(self::STATUS);
    }

    public function getCustomerNote(): string
    {
        return trim((string) $this->getData(self::CUSTOMER_NOTE));
    }

    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): string
    {
        return (string) $this->getData(self::UPDATED_AT);
    }

    public function setCustomerId(int $customerId): OfferRequestInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function setStatus(string $status): OfferRequestInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setCustomerNote(string $customerNote): OfferRequestInterface
    {
        return $this->setData(self::CUSTOMER_NOTE, trim($customerNote));
    }
}
