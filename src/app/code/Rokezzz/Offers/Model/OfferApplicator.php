<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Rokezzz\Offers\Api\OfferApplicatorInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Model\Quote\OfferQuoteItemManager;

class OfferApplicator implements OfferApplicatorInterface
{
    public const BUY_REQUEST_OFFER_ID = 'rokezzz_offer_id';
    public const BUY_REQUEST_OFFER_ITEM_ID = 'rokezzz_offer_item_id';
    public const BUY_REQUEST_OFFER_PRICE = 'rokezzz_offer_price';

    public function __construct(
        private readonly OfferProviderInterface $offerProvider,
        private readonly OfferStatusValidator $offerStatusValidator,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CheckoutSession $checkoutSession,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly OfferQuoteItemManager $offerQuoteItemManager
    ) {
    }

    public function applyToCart(int $offerId, int $customerId): void
    {
        $offer = $this->offerProvider->getByIdForCustomer($offerId, $customerId);
        $this->offerStatusValidator->assertOfferIsUsable($offer);

        $quote = $this->checkoutSession->getQuote();

        if ((int) $quote->getCustomerId() !== $customerId) {
            throw new LocalizedException(__('Unable to apply this offer to your cart.'));
        }

        if ($this->offerQuoteItemManager->quoteContainsOfferId($quote, $offerId)) {
            throw new LocalizedException(__('This offer has already been added to your cart.'));
        }

        $offerItems = $this->offerProvider->getItemsByOfferId($offerId);

        if ($offerItems === []) {
            throw new LocalizedException(__('This offer does not contain any items.'));
        }

        foreach ($offerItems as $offerItem) {
            $offerItemEntityId = $offerItem->getEntityId();

            if (is_null($offerItemEntityId)) {
                continue;
            }

            $product = $this->productRepository->getById($offerItem->getProductId());
            $buyRequest = new DataObject([
                'qty' => $offerItem->getOfferedQty(),
                self::BUY_REQUEST_OFFER_ID => $offerId,
                self::BUY_REQUEST_OFFER_ITEM_ID => $offerItemEntityId,
                self::BUY_REQUEST_OFFER_PRICE => $offerItem->getOfferedPrice(),
            ]);
            $quote->addProduct($product, $buyRequest);
        }

        $quote->setData(OfferQuoteItemManager::QUOTE_ACTIVE_OFFER_ID, $offerId);
        $this->cartRepository->save($quote);
    }
}
