<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

class RedemptionCalculator
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getMonetaryDiscount(int $points, int $storeId): float
    {
        if ($points <= 0) {
            return 0.0;
        }

        return round($points * $this->config->getPointValue($storeId), 2);
    }

    public function getMaxRedeemablePoints(int $customerBalance, float $quoteGrandTotal, int $storeId): int
    {
        if ($customerBalance <= 0 || $quoteGrandTotal <= 0) {
            return 0;
        }

        $pointValue = $this->config->getPointValue($storeId);

        if ($pointValue <= 0) {
            return 0;
        }

        $maxDiscount = $quoteGrandTotal * ($this->config->getMaxRedemptionPercent($storeId) / 100);
        $maxPointsFromPercent = (int) floor($maxDiscount / $pointValue);

        return max(0, min($customerBalance, $maxPointsFromPercent));
    }

    public function resolveAppliedPoints(
        int $requestedPoints,
        int $customerBalance,
        float $quoteGrandTotal,
        int $storeId
    ): int {
        if ($requestedPoints <= 0) {
            return 0;
        }

        $minPoints = $this->config->getMinPointsToRedeem($storeId);

        if ($requestedPoints < $minPoints) {
            return 0;
        }

        $maxPoints = $this->getMaxRedeemablePoints($customerBalance, $quoteGrandTotal, $storeId);

        return max(0, min($requestedPoints, $maxPoints));
    }
}
