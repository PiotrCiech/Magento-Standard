<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\OrderOwnerNotification\Api\OrderOwnerNotifierInterface;

final class SendOrderOwnerNotification implements ObserverInterface
{
    public function __construct(
        private readonly OrderOwnerNotifierInterface $orderOwnerNotifier
    ) {
    }

    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->orderOwnerNotifier->notify($order);
    }
}
