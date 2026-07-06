<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

class QuoteManagementPlugin
{
    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly QuotePointsApplier $quotePointsApplier,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
    }

    /**
     * @param int|string $orderId
     * @param int|string $cartId
     */
    public function afterPlaceOrder(
        QuoteManagement $subject,
        $orderId,
        $cartId,
        ?PaymentInterface $paymentMethod = null
    ): int {
        $orderId = (int) $orderId;

        if ($orderId <= 0) {
            return $orderId;
        }

        $cart = $this->quoteRepository->get((int) $cartId);

        if (!$cart instanceof Quote) {
            return $orderId;
        }

        $order = $this->orderRepository->get($orderId);

        if (!$order instanceof Order) {
            return $orderId;
        }

        $pointsUsed = $this->quotePointsApplier->getAppliedPoints($cart);
        $storeId = (int) $order->getStoreId();
        $discount = $this->redemptionCalculator->getMonetaryDiscount($pointsUsed, $storeId);

        $order->setData(QuotePointsApplier::QUOTE_POINTS_FIELD, $pointsUsed);
        $order->setData('rokezzz_loyalty_points_discount', $discount);
        $this->orderRepository->save($order);

        return $orderId;
    }
}
