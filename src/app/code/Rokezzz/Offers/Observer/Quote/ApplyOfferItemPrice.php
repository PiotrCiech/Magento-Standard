<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Observer\Quote;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\OfferApplicator;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;

class ApplyOfferItemPrice implements ObserverInterface
{
    public function __construct(
        private readonly OfferQuoteItemManager $offerQuoteItemManager
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

            $offerId = (int) $buyRequest->getData(OfferApplicator::BUY_REQUEST_OFFER_ID);
            $offerItemId = (int) $buyRequest->getData(OfferApplicator::BUY_REQUEST_OFFER_ITEM_ID);
            $offerPrice = (float) $buyRequest->getData(OfferApplicator::BUY_REQUEST_OFFER_PRICE);

            if ($offerId <= 0 || $offerItemId <= 0) {
                continue;
            }

            $this->offerQuoteItemManager->persistMetadata($item, $offerId, $offerItemId, $offerPrice);
            $this->offerQuoteItemManager->applyOfferPrice($item);

            $quote = $item->getQuote();

            if (!is_null($quote)) {
                $quote->setData(OfferQuoteItemManager::QUOTE_ACTIVE_OFFER_ID, $offerId);
            }
        }
    }
}
