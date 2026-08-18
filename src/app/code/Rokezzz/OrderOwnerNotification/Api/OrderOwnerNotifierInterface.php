<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderOwnerNotifierInterface
{
    public function notify(OrderInterface $order): void;
}
