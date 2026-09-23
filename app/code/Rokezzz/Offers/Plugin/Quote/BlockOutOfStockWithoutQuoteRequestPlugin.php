<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Plugin\Quote;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Rokezzz\Offers\Model\Product\QuoteRequestEligibilityChecker;
use Rokezzz\Offers\Model\Quote\QuoteRequestOperationContext;

class BlockOutOfStockWithoutQuoteRequestPlugin
{
    public function __construct(
        private readonly QuoteRequestEligibilityChecker $quoteRequestEligibilityChecker,
        private readonly QuoteRequestOperationContext $quoteRequestOperationContext
    ) {
    }

    /**
     * @param mixed $request
     *
     * @throws LocalizedException
     */
    public function beforeAddProduct(Quote $quote, Product $product, $request = null): array
    {
        $buyRequest = $this->normalizeBuyRequest($request);

        if (!$this->quoteRequestOperationContext->isActive()) {
            $buyRequest = null;
        }

        $this->quoteRequestEligibilityChecker->assertCanAddViaStandardCart($product, $buyRequest);

        return [$product, $request];
    }

    private function normalizeBuyRequest(mixed $request): ?DataObject
    {
        if ($request instanceof DataObject) {
            return $request;
        }

        if (is_array($request)) {
            return new DataObject($request);
        }

        return null;
    }
}
