<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

final class OrderNumberSaver
{
    public const QUOTE_FIELD = 'rokezzz_customer_order_number';

    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly Config $config
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

    public function save(int $cartId, ?string $orderNumber, ?int $storeId = null): void
    {
        if (is_null($orderNumber)) {
            return;
        }

        $orderNumber = trim($orderNumber);

        if ($orderNumber === '') {
            return;
        }

        $maxLength = $this->config->getOrderNumberMaxLength($storeId);

        if (mb_strlen($orderNumber) > $maxLength) {
            $orderNumber = mb_substr($orderNumber, 0, $maxLength);
        }

        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote instanceof Quote) {
            return;
        }

        $quote->setData(self::QUOTE_FIELD, $orderNumber);
        $this->quoteRepository->save($quote);
    }
}
