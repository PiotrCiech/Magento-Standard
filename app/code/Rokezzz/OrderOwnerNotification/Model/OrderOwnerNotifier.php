<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\OrderOwnerNotification\Api\OrderOwnerNotifierInterface;

class OrderOwnerNotifier implements OrderOwnerNotifierInterface
{
    public function __construct(
        private readonly OrderOwnerNotificationContextResolver $contextResolver,
        private readonly OrderOwnerNotificationEmailSender $emailSender
    ) {
    }

    public function notify(OrderInterface $order): void
    {
        $context = $this->contextResolver->resolve($order);

        if (is_null($context)) {
            return;
        }

        $this->emailSender->send($context);
    }
}
