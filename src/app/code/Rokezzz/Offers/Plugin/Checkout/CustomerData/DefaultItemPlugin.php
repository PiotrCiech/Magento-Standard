<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\DefaultItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class DefaultItemPlugin
{
    public function __construct(
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager,
        private readonly OfferQuoteItemManager $offerQuoteItemManager
    ) {
    }

    public function afterGetItemData(DefaultItem $subject, array $result, \Magento\Quote\Model\Quote\Item $item): array
    {
        if (!$item instanceof AbstractItem) {
            return $result;
        }

        if ($this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
            $result['rokezzz_item_badge_label'] = (string) __('Quote Request');
            $result['rokezzz_item_badge_modifier'] = 'rokezzz-cart-item-badge rokezzz-cart-item-badge--quote-request';

            return $result;
        }

        if ($this->offerQuoteItemManager->isOfferItem($item)) {
            $result['rokezzz_item_badge_label'] = (string) __('Offer Price');
            $result['rokezzz_item_badge_modifier'] = 'rokezzz-cart-item-badge rokezzz-cart-item-badge--offer';
        }

        return $result;
    }
}
