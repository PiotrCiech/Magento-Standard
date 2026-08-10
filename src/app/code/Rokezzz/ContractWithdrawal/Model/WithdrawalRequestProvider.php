<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use Rokezzz\ContractWithdrawal\Model\ResourceModel\WithdrawalRequestResource;

final class WithdrawalRequestProvider
{
    public function __construct(
        private readonly WithdrawalRequestCreator $withdrawalRequestCreator,
        private readonly WithdrawalRequestResource $withdrawalRequestResource
    ) {
    }

    public function getByOrderId(int $orderId): ?WithdrawalRequest
    {
        $withdrawalRequest = $this->withdrawalRequestCreator->create();
        $this->withdrawalRequestResource->load($withdrawalRequest, $orderId, 'order_id');

        if (is_null($withdrawalRequest->getEntityId())) {
            return null;
        }

        return $withdrawalRequest;
    }
}
