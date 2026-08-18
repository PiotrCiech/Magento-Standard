<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class OfferQuoteItemManager
{
    public const OPTION_OFFER_ID = 'rokezzz_offer_id';
    public const OPTION_OFFER_ITEM_ID = 'rokezzz_offer_item_id';
    public const OPTION_OFFER_PRICE = 'rokezzz_offer_price';
    public const QUOTE_ACTIVE_OFFER_ID = 'rokezzz_active_offer_id';

    public function isOfferItem(AbstractItem $item): bool
    {
        return !is_null($this->getOfferId($item));
    }

    public function getOfferId(AbstractItem $item): ?int
    {
        $value = $this->getOptionValue($item, self::OPTION_OFFER_ID);

        if (is_null($value) || $value === '') {
            return null;
        }

        return (int) $value;
    }

    public function getOfferItemId(AbstractItem $item): ?int
    {
        $value = $this->getOptionValue($item, self::OPTION_OFFER_ITEM_ID);

        if (is_null($value) || $value === '') {
            return null;
        }

        return (int) $value;
    }

    public function getOfferPrice(AbstractItem $item): ?float
    {
        $value = $this->getOptionValue($item, self::OPTION_OFFER_PRICE);

        if (is_null($value) || $value === '') {
            return null;
        }

        return (float) $value;
    }

    public function persistMetadata(AbstractItem $item, int $offerId, int $offerItemId, float $offerPrice): void
    {
        $this->setOptionValue($item, self::OPTION_OFFER_ID, (string) $offerId);
        $this->setOptionValue($item, self::OPTION_OFFER_ITEM_ID, (string) $offerItemId);
        $this->setOptionValue($item, self::OPTION_OFFER_PRICE, (string) $offerPrice);
    }

    public function applyOfferPrice(AbstractItem $item): void
    {
        $offerPrice = $this->getOfferPrice($item);

        if (is_null($offerPrice)) {
            return;
        }

        $item->setCustomPrice($offerPrice);
        $item->setOriginalCustomPrice($offerPrice);
        $item->getProduct()->setIsSuperMode(true);
    }

    public function getActiveOfferIdFromQuote(Quote $quote): ?int
    {
        $offerId = $quote->getData(self::QUOTE_ACTIVE_OFFER_ID);

        if (is_null($offerId) || $offerId === '') {
            return null;
        }

        return (int) $offerId;
    }

    public function quoteContainsOfferId(Quote $quote, int $offerId): bool
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item instanceof AbstractItem && $this->getOfferId($item) === $offerId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int[]
     */
    public function getOfferIdsFromQuote(Quote $quote): array
    {
        $offerIds = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            if (!$item instanceof AbstractItem) {
                continue;
            }

            $offerId = $this->getOfferId($item);

            if (!is_null($offerId)) {
                $offerIds[$offerId] = $offerId;
            }
        }

        return array_values($offerIds);
    }

    private function getOptionValue(AbstractItem $item, string $code): ?string
    {
        $option = $item->getOptionByCode($code);

        if (is_null($option)) {
            return null;
        }

        $value = $option->getValue();

        return is_null($value) ? null : (string) $value;
    }

    private function setOptionValue(AbstractItem $item, string $code, string $value): void
    {
        $item->addOption([
            'code' => $code,
            'value' => $value,
        ]);
    }
}
