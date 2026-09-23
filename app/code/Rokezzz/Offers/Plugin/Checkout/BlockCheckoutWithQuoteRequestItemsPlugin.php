<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Checkout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\QuoteManagement;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class BlockCheckoutWithQuoteRequestItemsPlugin
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function beforePlaceOrder(QuoteManagement $subject, int $cartId, ...$args): array
    {
        $this->assertCartHasNoQuoteRequestItems($cartId);

        return array_merge([$cartId], $args);
    }

    /**
     * @throws LocalizedException
     */
    private function assertCartHasNoQuoteRequestItems(int $cartId): void
    {
        $quote = $this->cartRepository->get($cartId);

        if (!$this->hasQuoteRequestItems($quote)) {
            return;
        }

        throw new LocalizedException(
            __('Please submit a quote request for out-of-stock items before checkout.')
        );
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
