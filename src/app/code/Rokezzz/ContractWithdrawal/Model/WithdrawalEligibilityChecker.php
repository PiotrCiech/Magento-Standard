<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Model;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class WithdrawalEligibilityChecker
{
    public function __construct(
        private readonly Config $config,
        private readonly WithdrawalRequestProvider $withdrawalRequestProvider,
        private readonly DateTime $dateTime
    ) {
    }

    public function canSubmit(OrderInterface $order, int $customerId): bool
    {
        try {
            $this->assertCanSubmit($order, $customerId);
        } catch (LocalizedException) {
            return false;
        }

        return true;
    }

    public function assertCanSubmit(OrderInterface $order, int $customerId): void
    {
        if (!$this->config->isEnabled((int) $order->getStoreId())) {
            throw new LocalizedException(__('Contract withdrawal requests are currently unavailable.'));
        }

        if ((int) $order->getCustomerId() !== $customerId) {
            throw new LocalizedException(__('You cannot submit a withdrawal request for this order.'));
        }

        if ((string) $order->getState() === Order::STATE_CANCELED) {
            throw new LocalizedException(__('A withdrawal request cannot be created for a cancelled order.'));
        }

        if (!is_null($this->withdrawalRequestProvider->getByOrderId((int) $order->getEntityId()))) {
            throw new LocalizedException(__('A withdrawal request has already been submitted for this order.'));
        }

        if (!$this->isWithinWithdrawalPeriod($order)) {
            throw new LocalizedException(__('The withdrawal period for this order has already expired.'));
        }
    }

    public function getDeadline(OrderInterface $order): ?DateTimeImmutable
    {
        $createdAt = trim((string) $order->getCreatedAt());

        if ($createdAt === '') {
            return null;
        }

        $createdAtDate = new DateTimeImmutable($createdAt, new DateTimeZone('UTC'));
        $deadline = $createdAtDate->setTime(23, 59, 59)->add(
            new DateInterval(sprintf('P%dD', $this->config->getWithdrawalPeriodDays((int) $order->getStoreId())))
        );

        return $deadline;
    }

    private function isWithinWithdrawalPeriod(OrderInterface $order): bool
    {
        $deadline = $this->getDeadline($order);

        if (is_null($deadline)) {
            return false;
        }

        $now = new DateTimeImmutable(
            $this->dateTime->gmtDate('Y-m-d H:i:s'),
            new DateTimeZone('UTC')
        );

        return $now <= $deadline;
    }
}
