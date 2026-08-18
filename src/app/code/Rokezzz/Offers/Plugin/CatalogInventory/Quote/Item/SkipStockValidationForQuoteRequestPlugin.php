<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\CatalogInventory\Quote\Item;

use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Model\Quote\QuoteRequestOperationContext;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class SkipStockValidationForQuoteRequestPlugin
{
    public function __construct(
        private readonly QuoteRequestOperationContext $quoteRequestOperationContext,
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager
    ) {
    }

    public function aroundValidate(
        QuantityValidator $subject,
        callable $proceed,
        Observer $observer
    ): mixed {
        $item = $observer->getEvent()->getItem();

        if ($this->quoteRequestOperationContext->isActive()) {
            return null;
        }

        if ($item instanceof AbstractItem && $this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
            return null;
        }

        return $proceed($observer);
    }
}
