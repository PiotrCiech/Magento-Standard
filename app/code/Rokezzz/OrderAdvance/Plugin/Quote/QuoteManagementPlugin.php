<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\OrderNumberRequestReader;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

final class QuoteManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly OrderNumberRequestReader $orderNumberRequestReader,
        private readonly OrderNumberSaver $orderNumberSaver
    ) {
    }

    /**
     * @param mixed $cartId
     */
    public function beforePlaceOrder(QuoteManagement $subject, $cartId): void
    {
        if (!$this->config->isOrderNumberEnabled()) {
            return;
        }

        $quoteId = (int) $cartId;
        $quote = $this->quoteRepository->getActive($quoteId);

        if (!$quote instanceof Quote) {
            return;
        }

        if ($this->orderNumberSaver->readFromQuote($quote) !== '') {
            return;
        }

        $orderNumber = $this->orderNumberRequestReader->fromCurrentRequest();
        $this->orderNumberSaver->save($quoteId, $orderNumber, (int) $quote->getStoreId());
    }
}
