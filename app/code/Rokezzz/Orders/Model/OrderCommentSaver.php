<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Model;

use Magento\Quote\Api\CartRepositoryInterface;

final class OrderCommentSaver
{
    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly Config $config
    ) {
    }

    public function save(int $cartId, ?string $comment, ?int $storeId = null): void
    {
        if (is_null($comment)) {
            return;
        }

        $comment = trim($comment);

        if ($comment === '') {
            return;
        }

        $maxLength = $this->config->getMaxLength($storeId);

        if (mb_strlen($comment) > $maxLength) {
            $comment = mb_substr($comment, 0, $maxLength);
        }

        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setCustomerNote($comment);
        $this->quoteRepository->save($quote);
    }
}
