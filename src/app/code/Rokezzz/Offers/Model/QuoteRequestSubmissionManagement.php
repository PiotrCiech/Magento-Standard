<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;
use Rokezzz\Offers\Api\OfferRequestManagementInterface;
use Rokezzz\Offers\Api\QuoteRequestSubmissionManagementInterface;
use Rokezzz\Offers\Model\Data\OfferRequestItemDataCreator;
use Rokezzz\Offers\Model\Quote\QuoteRequestQuoteItemManager;

class QuoteRequestSubmissionManagement implements QuoteRequestSubmissionManagementInterface
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly QuoteRequestQuoteItemManager $quoteRequestQuoteItemManager,
        private readonly OfferRequestManagementInterface $offerRequestManagement,
        private readonly OfferRequestItemDataCreator $offerRequestItemDataCreator
    ) {
    }

    public function submitFromCart(int $customerId, string $customerNote): OfferRequestInterface
    {
        $this->assertCustomerIsLoggedIn($customerId);

        $quote = $this->checkoutSession->getQuote();
        $this->assertQuoteBelongsToCustomer($quote, $customerId);

        $quoteRequestItems = $this->getQuoteRequestItems($quote);

        if ($quoteRequestItems === []) {
            throw new LocalizedException(__('Your cart does not contain any quote request items.'));
        }

        $offerRequest = $this->offerRequestManagement->submit(
            $customerId,
            $customerNote,
            $this->buildRequestItems($quoteRequestItems)
        );

        $this->removeQuoteRequestItems($quote, $quoteRequestItems);
        $this->cartRepository->save($quote);

        return $offerRequest;
    }

    /**
     * @throws LocalizedException
     */
    private function assertCustomerIsLoggedIn(int $customerId): void
    {
        if ($customerId <= 0) {
            throw new LocalizedException(__('You must be logged in to submit an offer request.'));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function assertQuoteBelongsToCustomer(Quote $quote, int $customerId): void
    {
        if ((int) $quote->getCustomerId() !== $customerId) {
            throw new LocalizedException(__('Unable to submit a quote request from this cart.'));
        }
    }

    /**
     * @return AbstractItem[]
     */
    private function getQuoteRequestItems(Quote $quote): array
    {
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && $this->quoteRequestQuoteItemManager->isQuoteRequestItem($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param AbstractItem[] $quoteRequestItems
     *
     * @return OfferRequestItemDataInterface[]
     */
    private function buildRequestItems(array $quoteRequestItems): array
    {
        $items = [];

        foreach ($quoteRequestItems as $quoteItem) {
            $itemData = $this->offerRequestItemDataCreator->create();
            $itemData->setProductId((int) $quoteItem->getProductId());
            $itemData->setQty((float) $quoteItem->getQty());
            $items[] = $itemData;
        }

        return $items;
    }

    /**
     * @param AbstractItem[] $quoteRequestItems
     */
    private function removeQuoteRequestItems(Quote $quote, array $quoteRequestItems): void
    {
        foreach ($quoteRequestItems as $quoteItem) {
            $quote->deleteItem($quoteItem);
        }
    }
}
