<?php

declare(strict_types=1);

namespace Rokezzz\Offers\ViewModel\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class CartQuoteRequest implements ArgumentInterface
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager,
        private readonly UrlInterface $url
    ) {
    }

    public function hasQuoteRequestItems(): bool
    {
        return $this->getQuoteRequestItems() !== [];
    }

    /**
     * @return AbstractItem[]
     */
    public function getQuoteRequestItems(): array
    {
        $quote = $this->checkoutSession->getQuote();
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && $this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getSubmitUrl(): string
    {
        return $this->url->getUrl('rokezzz_offers/request/submitFromCart');
    }

    public function getHistoryUrl(): string
    {
        return $this->url->getUrl('rokezzz_offers/request/history');
    }

    public function hasRegularItems(): bool
    {
        $quote = $this->checkoutSession->getQuote();

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && !$this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
                return true;
            }
        }

        return false;
    }

    public function getCheckoutBlockedMessage(): string
    {
        return (string) __('Please submit a quote request for out-of-stock items before checkout.');
    }
}
