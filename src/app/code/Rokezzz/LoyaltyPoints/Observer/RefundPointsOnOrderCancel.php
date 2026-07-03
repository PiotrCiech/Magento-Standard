<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;

class RefundPointsOnOrderCancel implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly BalanceManagementInterface $balanceManagement
    ) {
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order instanceof Order) {
            return;
        }

        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        $customerId = (int) $order->getCustomerId();

        if ($customerId <= 0) {
            return;
        }

        $pointsUsed = (int) $order->getData(QuotePointsApplier::QUOTE_POINTS_FIELD);
        $pointsEarned = (int) $order->getData('rokezzz_loyalty_points_earned');

        if ($pointsUsed > 0) {
            $this->balanceManagement->addPoints(
                $customerId,
                $pointsUsed,
                TransactionInterface::ACTION_CANCEL_REDEEM,
                $storeId,
                (int) $order->getEntityId(),
                (string) $order->getIncrementId(),
                (string) __('Refund of redeemed points for cancelled order %1', $order->getIncrementId())
            );
            $order->setData(QuotePointsApplier::QUOTE_POINTS_FIELD, 0);
            $order->getResource()->saveAttribute($order, QuotePointsApplier::QUOTE_POINTS_FIELD);
        }

        if ($pointsEarned > 0) {
            $this->balanceManagement->deductPoints(
                $customerId,
                $pointsEarned,
                TransactionInterface::ACTION_CANCEL_EARN,
                $storeId,
                (int) $order->getEntityId(),
                (string) $order->getIncrementId(),
                (string) __('Reversal of earned points for cancelled order %1', $order->getIncrementId())
            );
            $order->setData('rokezzz_loyalty_points_earned', 0);
            $order->getResource()->saveAttribute($order, 'rokezzz_loyalty_points_earned');
        }
    }
}
