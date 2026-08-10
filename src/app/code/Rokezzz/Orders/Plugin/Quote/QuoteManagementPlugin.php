<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentRequestReader;
use Rokezzz\Orders\Model\OrderCommentSaver;

final class QuoteManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly OrderCommentRequestReader $orderCommentRequestReader,
        private readonly OrderCommentSaver $orderCommentSaver
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

        if (trim((string) $quote->getCustomerNote()) !== '') {
            return;
        }

        $comment = $this->orderCommentRequestReader->fromCurrentRequest();
        $this->orderCommentSaver->save($quoteId, $comment);
    }
}
