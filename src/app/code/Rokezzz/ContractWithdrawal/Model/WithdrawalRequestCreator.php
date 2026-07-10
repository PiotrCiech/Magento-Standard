<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use Magento\Framework\ObjectManagerInterface;

class WithdrawalRequestCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data = []): WithdrawalRequest
    {
        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $this->objectManager->create(WithdrawalRequest::class, $data);

        return $withdrawalRequest;
    }
}
