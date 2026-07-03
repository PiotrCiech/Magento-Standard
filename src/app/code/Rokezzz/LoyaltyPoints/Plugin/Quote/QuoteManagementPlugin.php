<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

class QuoteManagementPlugin
{
    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
    }

    /**
     * @param mixed $cartId
     */
    public function afterPlaceOrder(QuoteManagement $subject, OrderInterface $order, $cartId): OrderInterface
    {
        $quote = $this->quoteRepository->get((int) $cartId);
        $pointsUsed = (int) $quote->getData(QuotePointsApplier::QUOTE_POINTS_FIELD);
        $storeId = (int) $order->getStoreId();
        $discount = $this->redemptionCalculator->getMonetaryDiscount($pointsUsed, $storeId);

        $order->setData(QuotePointsApplier::QUOTE_POINTS_FIELD, $pointsUsed);
        $order->setData('rokezzz_loyalty_points_discount', $discount);
        $order->save();

        return $order;
    }
}
