<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

final class OrderOwnerNotificationContextResolver
{
    public function __construct(
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {
    }

    public function resolve(OrderInterface $order): ?OrderOwnerNotificationContext
    {
        $storeId = (int) $order->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return null;
        }

        $recipientEmail = $this->config->getRecipientEmail($storeId);

        if (!$this->isValidEmail($recipientEmail)) {
            $this->logger->warning(
                'Order owner notification skipped: recipient email is not configured.',
                ['order_id' => $order->getEntityId(), 'store_id' => $storeId]
            );

            return null;
        }

        if (!$order instanceof Order) {
            $this->logger->warning(
                'Order owner notification skipped: unsupported order implementation.',
                ['order_id' => $order->getEntityId()]
            );

            return null;
        }

        return new OrderOwnerNotificationContext($order, $storeId, $recipientEmail);
    }

    private function isValidEmail(string $email): bool
    {
        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
