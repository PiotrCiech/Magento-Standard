<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\LoyaltyPointsAttribute;
use Rokezzz\LoyaltyPoints\Model\OrderAttributeSaver;
use Rokezzz\LoyaltyPoints\Model\PointsCalculator;

class AwardPointsOnOrderComplete implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly PointsCalculator $pointsCalculator,
        private readonly BalanceManagementInterface $balanceManagement,
        private readonly OrderAttributeSaver $orderAttributeSaver
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

        if ((int) $order->getData(LoyaltyPointsAttribute::POINTS_EARNED) > 0) {
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

        $order->setData(LoyaltyPointsAttribute::POINTS_EARNED, $points);
        $this->orderAttributeSaver->saveAttribute($order, LoyaltyPointsAttribute::POINTS_EARNED);
    }
}
