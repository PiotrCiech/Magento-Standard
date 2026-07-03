<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;

class QuotePointsApplier
{
    public const QUOTE_POINTS_FIELD = 'rokezzz_loyalty_points_used';

    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly Config $config,
        private readonly BalanceManagementInterface $balanceManagement,
        private readonly RedemptionCalculator $redemptionCalculator
    ) {
    }

    public function apply(Quote $quote, int $requestedPoints): int
    {
        $storeId = (int) $quote->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            $this->clear($quote);

            return 0;
        }

        $customerId = (int) $quote->getCustomerId();

        if ($customerId <= 0) {
            throw new LocalizedException(__('Loyalty points can only be used by logged-in customers.'));
        }

        $customerBalance = $this->balanceManagement->getBalance($customerId);

        $quote->setData(self::QUOTE_POINTS_FIELD, 0);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();

        $baseGrandTotal = (float) $quote->getGrandTotal();
        $appliedPoints = $this->redemptionCalculator->resolveAppliedPoints(
            $requestedPoints,
            $customerBalance,
            $baseGrandTotal,
            $storeId
        );

        $quote->setData(self::QUOTE_POINTS_FIELD, $appliedPoints);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);

        return $appliedPoints;
    }

    public function clear(Quote $quote): void
    {
        $quote->setData(self::QUOTE_POINTS_FIELD, 0);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
    }

    public function getAppliedPoints(Quote $quote): int
    {
        return (int) $quote->getData(self::QUOTE_POINTS_FIELD);
    }
}
