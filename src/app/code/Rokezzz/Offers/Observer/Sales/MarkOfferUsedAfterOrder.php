<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Rokezzz\Offers\Model\OfferUsageManager;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;

class MarkOfferUsedAfterOrder implements ObserverInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly OfferQuoteItemManager $offerQuoteItemManager,
        private readonly OfferUsageManager $offerUsageManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getData('order');

        if (!$order instanceof Order) {
            return;
        }

        $offerIds = $this->getOfferIdsFromOrder($order);

        foreach ($offerIds as $offerId) {
            try {
                $this->offerUsageManager->markAsUsed($offerId);
            } catch (\Throwable $throwable) {
                $this->logger->error(
                    'Failed to mark offer as used after order placement.',
                    [
                        'offer_id' => $offerId,
                        'order_id' => $order->getEntityId(),
                        'exception' => $throwable,
                    ]
                );
            }
        }
    }

    /**
     * @return int[]
     */
    private function getOfferIdsFromOrder(Order $order): array
    {
        $quoteId = (int) $order->getQuoteId();

        if ($quoteId <= 0) {
            return [];
        }

        try {
            $quote = $this->cartRepository->get($quoteId);

            return $this->offerQuoteItemManager->getOfferIdsFromQuote($quote);
        } catch (\Throwable) {
            return [];
        }
    }
}
