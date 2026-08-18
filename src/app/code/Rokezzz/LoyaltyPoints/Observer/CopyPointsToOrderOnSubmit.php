<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Rokezzz\LoyaltyPoints\Model\OrderPointsTransfer;

final class CopyPointsToOrderOnSubmit implements ObserverInterface
{
    public function __construct(
        private readonly OrderPointsTransfer $orderPointsTransfer
    ) {
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if (!$order instanceof Order || !$quote instanceof Quote) {
            return;
        }

        $this->orderPointsTransfer->copyFromQuote($quote, $order);
    }
}
