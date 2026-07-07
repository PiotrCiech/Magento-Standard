<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\ContractWithdrawal\Api\Data\WithdrawalRequestInterface;

class WithdrawalRequest extends AbstractModel implements WithdrawalRequestInterface
{
    protected function _construct(): void
    {
        $this->_init(\Rokezzz\ContractWithdrawal\Model\ResourceModel\WithdrawalRequest::class);
    }

    public function getEntityId(): ?int
    {
        $entityId = $this->getData(self::ENTITY_ID);

        return $entityId === null ? null : (int) $entityId;
    }

    public function getOrderId(): int
    {
        return (int) $this->getData(self::ORDER_ID);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    public function getStatus(): string
    {
        return (string) $this->getData(self::STATUS);
    }

    public function getStatementText(): string
    {
        return trim((string) $this->getData(self::STATEMENT_TEXT));
    }

    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    public function setOrderId(int $orderId): WithdrawalRequestInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function setCustomerId(int $customerId): WithdrawalRequestInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function setStatus(string $status): WithdrawalRequestInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setStatementText(string $statementText): WithdrawalRequestInterface
    {
        return $this->setData(self::STATEMENT_TEXT, trim($statementText));
    }
}
