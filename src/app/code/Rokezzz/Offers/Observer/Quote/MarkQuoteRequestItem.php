<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Observer\Quote;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class MarkQuoteRequestItem implements ObserverInterface
{
    public function __construct(
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var AbstractItem[] $items */
        $items = (array) $observer->getEvent()->getData('items');

        foreach ($items as $item) {
            if (!$item instanceof AbstractItem) {
                continue;
            }

            $buyRequest = $item->getBuyRequest();

            if (!$buyRequest instanceof DataObject) {
                continue;
            }

            if (!(bool) $buyRequest->getData(QuoteRequestQuoteItemManager::BUY_REQUEST_FLAG)) {
                continue;
            }

            $this->quoteRequestQuoteItemManager->persistMetadata($item);
        }
    }
}
