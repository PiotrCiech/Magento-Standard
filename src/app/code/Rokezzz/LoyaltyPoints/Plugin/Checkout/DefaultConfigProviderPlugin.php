<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\LoyaltyPoints\Api\BalanceManagementInterface;
use Rokezzz\LoyaltyPoints\Model\Config;
use Rokezzz\LoyaltyPoints\Model\QuotePointsApplier;
use Rokezzz\LoyaltyPoints\Model\RedemptionCalculator;

class DefaultConfigProviderPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly BalanceManagementInterface $balanceManagement,
        private readonly RedemptionCalculator $redemptionCalculator,
        private readonly CustomerSession $customerSession,
        private readonly CheckoutSession $checkoutSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly QuotePointsApplier $quotePointsApplier
    ) {
    }

    public function afterGetConfig(DefaultConfigProvider $subject, array $result): array
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $enabled = $this->config->isEnabled($storeId);
        $balance = 0;
        $maxRedeemablePoints = 0;

        $quote = $this->checkoutSession->getQuote();
        $appliedPoints = $this->quotePointsApplier->getAppliedPoints($quote);

        if ($enabled && $this->customerSession->isLoggedIn()) {
            $customerId = (int) $this->customerSession->getCustomerId();
            $balance = $this->balanceManagement->getBalance($customerId);

            $loyaltyDiscount = $this->redemptionCalculator->getMonetaryDiscount($appliedPoints, $storeId);
            $baseGrandTotal = (float) $quote->getGrandTotal() + $loyaltyDiscount;

            $maxRedeemablePoints = $this->redemptionCalculator->getMaxRedeemablePoints(
                $balance,
                $baseGrandTotal,
                $storeId
            );
        }

        $result['rokezzzLoyaltyPoints'] = [
            'enabled' => $enabled,
            'balance' => $balance,
            'appliedPoints' => $appliedPoints,
            'minPoints' => $this->config->getMinPointsToRedeem($storeId),
            'maxRedeemablePoints' => $maxRedeemablePoints,
            'pointValue' => $this->config->getPointValue($storeId),
            'applyUrl' => 'loyaltypoints/checkout/apply',
            'cancelUrl' => 'loyaltypoints/checkout/cancel',
        ];

        return $result;
    }
}
