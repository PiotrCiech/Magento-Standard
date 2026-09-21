<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class DefaultConfigProviderPlugin
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager,
        private readonly UrlInterface $url
    ) {
    }

    public function afterGetConfig(DefaultConfigProvider $subject, array $result): array
    {
        $quote = $this->checkoutSession->getQuote();
        $hasQuoteRequestItems = $this->hasQuoteRequestItems($quote);

        $result['rokezzzOffers'] = [
            'hasQuoteRequestItems' => $hasQuoteRequestItems,
            'checkoutBlockedMessage' => (string) __(
                'Please submit a quote request for out-of-stock items before checkout.'
            ),
            'cartUrl' => $this->url->getUrl('checkout/cart'),
            'cartLinkLabel' => (string) __('Return to cart'),
        ];

        return $result;
    }

    private function hasQuoteRequestItems(Quote $quote): bool
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && $this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
                return true;
            }
        }

        return false;
    }
}
