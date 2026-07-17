<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model\Quote\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

class LoyaltyPoints extends AbstractTotal
{
    public const CODE = 'rokezzz_loyalty_points';

    public function __construct(
        private readonly Config $config,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
        $this->setCode(self::CODE);
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): self {
        parent::collect($quote, $shippingAssignment, $total);

        $storeId = (int) $quote->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return $this;
        }

        $pointsUsed = (int) $quote->getData(QuotePointsApplier::QUOTE_POINTS_FIELD);

        if ($pointsUsed <= 0) {
            return $this;
        }

        $discount = $this->redemptionCalculator->getMonetaryDiscount($pointsUsed, $storeId);

        if ($discount <= 0) {
            return $this;
        }

        $total->addTotalAmount(self::CODE, -$discount);
        $total->addBaseTotalAmount(self::CODE, -$discount);

        return $this;
    }

    public function fetch(Quote $quote, Total $total): array
    {
        $storeId = (int) $quote->getStoreId();
        $pointsUsed = (int) $quote->getData(QuotePointsApplier::QUOTE_POINTS_FIELD);
        $discount = $this->redemptionCalculator->getMonetaryDiscount($pointsUsed, $storeId);

        if (!$this->config->isEnabled($storeId) || $discount <= 0) {
            return [];
        }

        return [
            'code' => self::CODE,
            'title' => (string) __('Loyalty Points (%1 pts)', $pointsUsed),
            'value' => -$discount,
        ];
    }
}
