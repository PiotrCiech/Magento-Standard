<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\ContractWithdrawal\Api\Data\WithdrawalRequestInterface;
use Rokezzz\ContractWithdrawal\Model\ResourceModel\WithdrawalRequest as WithdrawalRequestResource;

class WithdrawalRequestManagement
{
    public function __construct(
        private readonly WithdrawalEligibilityChecker $withdrawalEligibilityChecker,
        private readonly WithdrawalRequestCreator $withdrawalRequestCreator,
        private readonly WithdrawalRequestResource $withdrawalRequestResource
    ) {
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function submit(OrderInterface $order, int $customerId, string $statementDetails = ''): WithdrawalRequest
    {
        $this->withdrawalEligibilityChecker->assertCanSubmit($order, $customerId);

        $withdrawalRequest = $this->withdrawalRequestCreator->create();
        $withdrawalRequest->setOrderId((int) $order->getEntityId());
        $withdrawalRequest->setCustomerId($customerId);
        $withdrawalRequest->setStatus(WithdrawalRequestInterface::STATUS_REQUESTED);
        $withdrawalRequest->setStatementText($this->buildStatementText($order, $statementDetails));

        try {
            $this->withdrawalRequestResource->save($withdrawalRequest);
        } catch (\Throwable $throwable) {
            throw new CouldNotSaveException(
                __('The withdrawal request could not be saved right now. Please try again later.'),
                $throwable
            );
        }

        return $withdrawalRequest;
    }

    private function buildStatementText(OrderInterface $order, string $statementDetails): string
    {
        $statementDetails = trim($statementDetails);

        if ($statementDetails !== '') {
            return $statementDetails;
        }

        return (string) __(
            'I hereby submit a withdrawal request for order #%1.',
            $order->getIncrementId()
        );
    }
}
