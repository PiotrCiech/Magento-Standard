<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\PointsCalculator;

class AwardPointsOnOrderComplete implements ObserverInterface
{
    private const EARNED_POINTS_FIELD = 'rokezzz_loyalty_points_earned';

    public function __construct(
        private readonly Config $config,
        private readonly PointsCalculator $pointsCalculator,
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

        if ($order->getState() !== Order::STATE_COMPLETE) {
            return;
        }

        if ($order->getOrigData('state') === Order::STATE_COMPLETE) {
            return;
        }

        if ((int) $order->getData(self::EARNED_POINTS_FIELD) > 0) {
            return;
        }

        $customerId = (int) $order->getCustomerId();

        if ($customerId <= 0) {
            return;
        }

        $points = $this->pointsCalculator->calculateEarnedPoints($order);

        if ($points <= 0) {
            return;
        }

        $this->balanceManagement->addPoints(
            $customerId,
            $points,
            TransactionInterface::ACTION_EARN,
            $storeId,
            (int) $order->getEntityId(),
            (string) $order->getIncrementId(),
            (string) __('Points for order %1', $order->getIncrementId())
        );

        $order->setData(self::EARNED_POINTS_FIELD, $points);
        $order->getResource()->saveAttribute($order, self::EARNED_POINTS_FIELD);
    }
}
