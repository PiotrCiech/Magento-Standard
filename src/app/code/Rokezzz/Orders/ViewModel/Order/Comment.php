<?php

declare(strict_types=1);

namespace Rokezzz\Orders\ViewModel\Order;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Comment implements ArgumentInterface
{
    public function getCustomerNote(OrderInterface $order): string
    {
        return trim((string) $order->getCustomerNote());
    }

    public function shouldDisplay(OrderInterface $order): bool
    {
        return $this->getCustomerNote($order) !== '';
    }
}
