<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;

class RedeemPointsOnOrderPlace implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly BalanceManagementInterface $balanceManagement,
        private readonly QuotePointsApplier $quotePointsApplier
    ) {
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if (!$order instanceof Order || !$quote instanceof Quote) {
            return;
        }

        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        $pointsUsed = $this->quotePointsApplier->getAppliedPoints($quote);

        if ($pointsUsed <= 0) {
            return;
        }

        $customerId = (int) $order->getCustomerId();

        if ($customerId <= 0) {
            return;
        }

        $this->balanceManagement->deductPoints(
            $customerId,
            $pointsUsed,
            TransactionInterface::ACTION_REDEEM,
            $storeId,
            (int) $order->getEntityId(),
            (string) $order->getIncrementId(),
            (string) __('Points redeemed for order %1', $order->getIncrementId())
        );
    }
}
