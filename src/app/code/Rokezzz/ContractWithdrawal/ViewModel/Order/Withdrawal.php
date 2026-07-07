<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\ViewModel\Order;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\ContractWithdrawal\Api\Data\WithdrawalRequestInterface;
use Rokezzz\ContractWithdrawal\Model\Config;
use Rokezzz\ContractWithdrawal\Model\WithdrawalEligibilityChecker;
use Rokezzz\ContractWithdrawal\Model\WithdrawalRequestProvider;

class Withdrawal implements ArgumentInterface
{
    /**
     * @var array<int, WithdrawalRequestInterface|null>
     */
    private array $withdrawalRequests = [];

    public function __construct(
        private readonly Config $config,
        private readonly CustomerSession $customerSession,
        private readonly UrlInterface $url,
        private readonly TimezoneInterface $timezone,
        private readonly WithdrawalEligibilityChecker $withdrawalEligibilityChecker,
        private readonly WithdrawalRequestProvider $withdrawalRequestProvider
    ) {
    }

    public function shouldDisplay(OrderInterface $order): bool
    {
        return $this->config->isEnabled((int) $order->getStoreId())
            || $this->hasWithdrawalRequest($order);
    }

    public function hasWithdrawalRequest(OrderInterface $order): bool
    {
        return $this->getWithdrawalRequest($order) !== null;
    }

    public function canSubmit(OrderInterface $order): bool
    {
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($customerId === 0) {
            return false;
        }

        return $this->withdrawalEligibilityChecker->canSubmit($order, $customerId);
    }

    public function getUnavailableReason(OrderInterface $order): string
    {
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($customerId === 0) {
            return (string) __('Please sign in to submit a withdrawal request.');
        }

        try {
            $this->withdrawalEligibilityChecker->assertCanSubmit($order, $customerId);
        } catch (LocalizedException $exception) {
            return $exception->getMessage();
        }

        return '';
    }

    public function getSubmitUrl(): string
    {
        return $this->url->getUrl('rokezzz_contract_withdrawal/order/submit');
    }

    public function getPeriodDays(OrderInterface $order): int
    {
        return $this->config->getWithdrawalPeriodDays((int) $order->getStoreId());
    }

    public function getFormattedDeadline(OrderInterface $order): string
    {
        $deadline = $this->withdrawalEligibilityChecker->getDeadline($order);

        if ($deadline === null) {
            return '';
        }

        return (string) $this->timezone->formatDateTime(
            $deadline,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::NONE
        );
    }

    public function getFormattedCreatedAt(OrderInterface $order): string
    {
        $withdrawalRequest = $this->getWithdrawalRequest($order);

        if ($withdrawalRequest === null) {
            return '';
        }

        return (string) $this->timezone->formatDateTime(
            new DateTimeImmutable($withdrawalRequest->getCreatedAt(), new DateTimeZone('UTC')),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::SHORT
        );
    }

    public function getStatementText(OrderInterface $order): string
    {
        $withdrawalRequest = $this->getWithdrawalRequest($order);

        return $withdrawalRequest?->getStatementText() ?? '';
    }

    public function getStatusLabel(OrderInterface $order): string
    {
        $status = $this->getWithdrawalRequest($order)?->getStatus();

        return match ($status) {
            WithdrawalRequestInterface::STATUS_ACCEPTED => (string) __('Accepted'),
            WithdrawalRequestInterface::STATUS_REJECTED => (string) __('Rejected'),
            WithdrawalRequestInterface::STATUS_COMPLETED => (string) __('Completed'),
            WithdrawalRequestInterface::STATUS_REQUESTED => (string) __('Requested'),
            default => '',
        };
    }

    private function getWithdrawalRequest(OrderInterface $order): ?WithdrawalRequestInterface
    {
        $orderId = (int) $order->getEntityId();

        if (!array_key_exists($orderId, $this->withdrawalRequests)) {
            $this->withdrawalRequests[$orderId] = $this->withdrawalRequestProvider->getByOrderId($orderId);
        }

        return $this->withdrawalRequests[$orderId];
    }
}
