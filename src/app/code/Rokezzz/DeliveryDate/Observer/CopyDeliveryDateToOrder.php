<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Observer;

use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Rokezzz\DeliveryDate\Model\Config;

class CopyDeliveryDateToOrder implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly Copy $objectCopyService
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if (!$order instanceof Order || !$quote instanceof Quote) {
            return;
        }

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote',
            'to_order',
            $quote,
            $order
        );
    }
}
