<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class QuoteRequestEligibilityChecker
{
    public function isQuoteRequestBuyRequest(?DataObject $buyRequest): bool
    {
        if (is_null($buyRequest)) {
            return false;
        }

        return (bool) $buyRequest->getData(QuoteRequestQuoteItemManager::BUY_REQUEST_FLAG);
    }

    public function requiresQuoteRequest(ProductInterface $product): bool
    {
        if (!$product instanceof Product) {
            return false;
        }

        return !$product->isSalable();
    }

    /**
     * @throws LocalizedException
     */
    public function assertRequiresQuoteRequest(ProductInterface $product): void
    {
        if (!$this->requiresQuoteRequest($product)) {
            throw new LocalizedException(__('This product can be purchased normally and does not require a quote request.'));
        }
    }

    /**
     * @throws LocalizedException
     */
    public function assertCanAddViaStandardCart(ProductInterface $product, ?DataObject $buyRequest): void
    {
        if ($this->isQuoteRequestBuyRequest($buyRequest)) {
            return;
        }

        if (!$this->requiresQuoteRequest($product)) {
            return;
        }

        throw new LocalizedException(
            __('This product is out of stock. Please use the quote request option instead.')
        );
    }
}
