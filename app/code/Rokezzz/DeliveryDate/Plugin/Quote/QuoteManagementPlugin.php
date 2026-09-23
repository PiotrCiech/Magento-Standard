<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Rokezzz\DeliveryDate\Model\Config;
use Rokezzz\DeliveryDate\Model\DeliveryDateRequestReader;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;
use Rokezzz\DeliveryDate\Model\DeliveryDateValidator;

final class QuoteManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly DeliveryDateRequestReader $deliveryDateRequestReader,
        private readonly DeliveryDateSaver $deliveryDateSaver,
        private readonly DeliveryDateValidator $deliveryDateValidator
    ) {
    }

    /**
     * @param mixed $cartId
     */
    public function beforePlaceOrder(QuoteManagement $subject, $cartId): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $quoteId = (int) $cartId;
        $quote = $this->quoteRepository->getActive($quoteId);

        if (!$quote instanceof Quote) {
            return;
        }

        $storeId = (int) $quote->getStoreId();
        $existingDate = $this->deliveryDateSaver->readFromQuote($quote);

        if ($existingDate === '') {
            $deliveryDate = $this->deliveryDateRequestReader->fromCurrentRequest();
            $this->deliveryDateSaver->save($quoteId, $deliveryDate, $storeId);
            $reloadedQuote = $this->quoteRepository->getActive($quoteId);

            if (!$reloadedQuote instanceof Quote) {
                return;
            }

            $existingDate = $this->deliveryDateSaver->readFromQuote($reloadedQuote);
        }

        $this->deliveryDateValidator->validate(
            $existingDate !== '' ? $existingDate : null,
            $storeId
        );
    }
}
