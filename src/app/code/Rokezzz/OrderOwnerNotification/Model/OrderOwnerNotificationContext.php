<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Sales\Model\Order;

readonly class OrderOwnerNotificationContext
{
    public function __construct(
        public Order $order,
        public int $storeId,
        public string $recipientEmail
    ) {
    }
}
