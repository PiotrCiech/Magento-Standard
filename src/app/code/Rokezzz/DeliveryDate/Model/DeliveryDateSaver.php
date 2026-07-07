<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class DeliveryDateSaver
{
    public const QUOTE_FIELD = 'rokezzz_preferred_delivery_date';

    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly DeliveryDateValidator $deliveryDateValidator
    ) {
    }

    public function readFromQuote(Quote $quote): string
    {
        return trim((string) $quote->getData(self::QUOTE_FIELD));
    }

    public function readFromOrder(Order $order): string
    {
        return trim((string) $order->getData(self::QUOTE_FIELD));
    }

    public function save(int $cartId, ?string $deliveryDate, ?int $storeId = null): void
    {
        $validatedDate = $this->deliveryDateValidator->validate($deliveryDate, $storeId);

        if (is_null($validatedDate)) {
            return;
        }

        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote instanceof Quote) {
            return;
        }

        $quote->setData(self::QUOTE_FIELD, $validatedDate);
        $this->quoteRepository->save($quote);
    }
}
