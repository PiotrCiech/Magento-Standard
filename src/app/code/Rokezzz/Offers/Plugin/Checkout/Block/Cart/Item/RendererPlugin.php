<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Checkout\Block\Cart\Item;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class RendererPlugin
{
    public function __construct(
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager,
        private readonly OfferQuoteItemManager $offerQuoteItemManager
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $result
     *
     * @return array<int, array<string, mixed>>
     */
    public function afterGetOptionList(Renderer $subject, array $result): array
    {
        $item = $subject->getItem();

        if (!$item instanceof AbstractItem) {
            return $result;
        }

        $badgeLabel = $this->resolveBadgeLabel($item);

        if ($badgeLabel === '') {
            return $result;
        }

        $result[] = [
            'label' => (string) __('Order Type'),
            'value' => $badgeLabel,
        ];

        return $result;
    }

    private function resolveBadgeLabel(AbstractItem $item): string
    {
        if ($this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
            return (string) __('Quote Request');
        }

        if ($this->offerQuoteItemManager->isOfferItem($item)) {
            return (string) __('Offer Price');
        }

        return '';
    }
}
