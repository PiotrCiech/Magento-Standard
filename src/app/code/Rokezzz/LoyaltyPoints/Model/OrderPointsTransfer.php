<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class OrderPointsTransfer
{
    public function __construct(
        private readonly Config $config,
        private readonly QuotePointsApplier $quotePointsApplier,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
    }

    public function copyFromQuote(Quote $quote, Order $order): void
    {
        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        $pointsUsed = $this->quotePointsApplier->getAppliedPoints($quote);
        $discount = $this->redemptionCalculator->getMonetaryDiscount($pointsUsed, $storeId);

        $order->setData(LoyaltyPointsAttribute::POINTS_USED, $pointsUsed);
        $order->setData(LoyaltyPointsAttribute::POINTS_DISCOUNT, $discount);
    }
}
