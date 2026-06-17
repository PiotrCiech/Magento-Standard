<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Sales\Model\Order;

class OrderOwnerNotificationContext
{
    public function __construct(
        public readonly Order $order,
        public readonly int $storeId,
        public readonly string $recipientEmail
    ) {
    }
}
