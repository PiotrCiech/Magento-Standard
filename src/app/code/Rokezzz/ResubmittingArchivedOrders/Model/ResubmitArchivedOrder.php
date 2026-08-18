<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Reorder\Data\ReorderOutput;
use Magento\Sales\Model\Reorder\Reorder;
use Rokezzz\ResubmittingArchivedOrders\Api\ResubmitArchivedOrderInterface;

class ResubmitArchivedOrder implements ResubmitArchivedOrderInterface
{
    public function __construct(
        private readonly ArchivedOrderResubmitValidator $validator,
        private readonly Reorder $reorder,
        private readonly CheckoutSession $checkoutSession
    ) {
    }

    public function execute(int $customerId, int $orderId): ReorderOutput
    {
        $order = $this->validator->getOrderForResubmit($customerId, $orderId);

        $output = $this->reorder->execute($order->getIncrementId(), (string) $order->getStoreId());
        $this->checkoutSession->setQuoteId($output->getCart()->getId());

        return $output;
    }
}
