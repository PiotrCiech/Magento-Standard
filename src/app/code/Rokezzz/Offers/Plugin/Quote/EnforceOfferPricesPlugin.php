<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;

class EnforceOfferPricesPlugin
{
    public function __construct(
        private readonly OfferQuoteItemManager $offerQuoteItemManager
    ) {
    }

    public function afterCollectTotals(Quote $quote, Quote $result): Quote
    {
        foreach ($result->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && $this->offerQuoteItemManager->isOfferItem($item)) {
                $this->offerQuoteItemManager->applyOfferPrice($item);
            }
        }

        return $result;
    }
}
