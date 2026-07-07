<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Sales\Api\Data\OrderInterface;

class PointsCalculator
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function calculateEarnedPoints(OrderInterface $order): int
    {
        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return 0;
        }

        $baseAmount = (float) $order->getGrandTotal() - (float) $order->getShippingInclTax();

        if ($baseAmount <= 0) {
            return 0;
        }

        return (int) floor($baseAmount * $this->config->getPointsPerCurrency($storeId));
    }
}
