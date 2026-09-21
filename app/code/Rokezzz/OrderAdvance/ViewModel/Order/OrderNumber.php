<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\ViewModel\Order;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

class OrderNumber implements ArgumentInterface
{
    public function __construct(
        private readonly OrderNumberSaver $orderNumberSaver,
        private readonly Registry $registry
    ) {
    }

    public function getOrderNumber(): string
    {
        $order = $this->getOrder();

        if (is_null($order)) {
            return '';
        }

        return $this->orderNumberSaver->readFromOrder($order);
    }

    public function shouldDisplay(): bool
    {
        return $this->getOrderNumber() !== '';
    }

    private function getOrder(): ?Order
    {
        $order = $this->registry->registry('current_order');

        return $order instanceof Order ? $order : null;
    }
}
