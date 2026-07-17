<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferResource;

class Offer extends AbstractModel implements OfferInterface
{
    protected function _construct(): void
    {
        $this->_init(OfferResource::class);
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

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function getStatus(): string
    {
        return (string) $this->getData(self::STATUS);
    }

    public function getIncrementId(): string
    {
        return trim((string) $this->getData(self::INCREMENT_ID));
    }

    public function getAccessToken(): string
    {
        return trim((string) $this->getData(self::ACCESS_TOKEN));
    }

    public function getCustomerNote(): string
    {
        return trim((string) $this->getData(self::CUSTOMER_NOTE));
    }

    public function getAdminNote(): string
    {
        return trim((string) $this->getData(self::ADMIN_NOTE));
    }

    public function getExpiresAt(): ?string
    {
        $expiresAt = $this->getData(self::EXPIRES_AT);

        return is_null($expiresAt) ? null : (string) $expiresAt;
    }

    public function getSentAt(): ?string
    {
        $sentAt = $this->getData(self::SENT_AT);

        return is_null($sentAt) ? null : (string) $sentAt;
    }

    public function getAcceptedAt(): ?string
    {
        $acceptedAt = $this->getData(self::ACCEPTED_AT);

        return is_null($acceptedAt) ? null : (string) $acceptedAt;
    }

    public function getUsedAt(): ?string
    {
        $usedAt = $this->getData(self::USED_AT);

        return is_null($usedAt) ? null : (string) $usedAt;
    }

    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): string
    {
        return (string) $this->getData(self::UPDATED_AT);
    }

    public function setRequestId(int $requestId): OfferInterface
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    public function setCustomerId(int $customerId): OfferInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function setStatus(string $status): OfferInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setIncrementId(string $incrementId): OfferInterface
    {
        return $this->setData(self::INCREMENT_ID, trim($incrementId));
    }

    public function setAccessToken(string $accessToken): OfferInterface
    {
        return $this->setData(self::ACCESS_TOKEN, trim($accessToken));
    }

    public function setCustomerNote(string $customerNote): OfferInterface
    {
        return $this->setData(self::CUSTOMER_NOTE, trim($customerNote));
    }

    public function setAdminNote(string $adminNote): OfferInterface
    {
        return $this->setData(self::ADMIN_NOTE, trim($adminNote));
    }

    public function setExpiresAt(?string $expiresAt): OfferInterface
    {
        return $this->setData(self::EXPIRES_AT, $expiresAt);
    }

    public function setSentAt(?string $sentAt): OfferInterface
    {
        return $this->setData(self::SENT_AT, $sentAt);
    }

    public function setAcceptedAt(?string $acceptedAt): OfferInterface
    {
        return $this->setData(self::ACCEPTED_AT, $acceptedAt);
    }

    public function setUsedAt(?string $usedAt): OfferInterface
    {
        return $this->setData(self::USED_AT, $usedAt);
    }
}
