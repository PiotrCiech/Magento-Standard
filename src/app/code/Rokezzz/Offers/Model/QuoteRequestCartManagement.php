<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Rokezzz\Offers\Api\QuoteRequestCartManagementInterface;
use Rokezzz\Offers\Model\Product\QuoteRequestEligibilityChecker;
use Rokezzz\Offers\Model\Quote\QuoteRequestOperationContext;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class QuoteRequestCartManagement implements QuoteRequestCartManagementInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly QuoteRequestEligibilityChecker $quoteRequestEligibilityChecker,
        private readonly QuoteRequestOperationContext $quoteRequestOperationContext,
        private readonly CheckoutSession $checkoutSession,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    public function addToCart(int $customerId, int $productId, float $qty): void
    {
        $this->assertCustomerIsLoggedIn($customerId);
        $this->assertQtyIsValid($qty);

        $product = $this->productRepository->getById($productId);
        $this->quoteRequestEligibilityChecker->assertRequiresQuoteRequest($product);

        $quote = $this->checkoutSession->getQuote();
        $this->assertQuoteBelongsToCustomer($quote->getCustomerId(), $customerId);

        $productForCart = $this->prepareProductForQuoteRequest($product);
        $buyRequest = new DataObject([
            'qty' => $qty,
            QuoteRequestQuoteItemManager::BUY_REQUEST_FLAG => 1,
        ]);

        $this->quoteRequestOperationContext->begin();

        try {
            $quote->addProduct($productForCart, $buyRequest);
            $this->cartRepository->save($quote);
        } finally {
            $this->quoteRequestOperationContext->end();
        }
    }

    /**
     * @throws LocalizedException
     */
    private function assertCustomerIsLoggedIn(int $customerId): void
    {
        if ($customerId <= 0) {
            throw new LocalizedException(__('You must be logged in to request a quote.'));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function assertQtyIsValid(float $qty): void
    {
        if ($qty <= 0) {
            throw new LocalizedException(__('Please enter a quantity greater than zero.'));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function assertQuoteBelongsToCustomer(?int $quoteCustomerId, int $customerId): void
    {
        if ((int) $quoteCustomerId !== $customerId) {
            throw new LocalizedException(__('Unable to update your cart right now.'));
        }
    }

    private function prepareProductForQuoteRequest(Product $product): Product
    {
        $productForCart = clone $product;
        $productForCart->setData('salable', true);

        return $productForCart;
    }
}
