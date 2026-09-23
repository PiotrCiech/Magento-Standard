<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\ViewModel\Order;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Rokezzz\DeliveryDate\Model\DeliveryDateFormatter;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;

class DeliveryDate implements ArgumentInterface
{
    public function __construct(
        private readonly DeliveryDateFormatter $deliveryDateFormatter,
        private readonly DeliveryDateSaver $deliveryDateSaver,
        private readonly Registry $registry
    ) {
    }

    public function getFormattedDeliveryDate(): string
    {
        $order = $this->getOrder();

        if (is_null($order)) {
            return '';
        }

        return $this->deliveryDateFormatter->formatForDisplay(
            $this->deliveryDateSaver->readFromOrder($order)
        );
    }

    public function shouldDisplay(): bool
    {
        $order = $this->getOrder();

        if (is_null($order)) {
            return false;
        }

        return $this->deliveryDateSaver->readFromOrder($order) !== '';
    }

    private function getOrder(): ?Order
    {
        $order = $this->registry->registry('current_order');

        return $order instanceof Order ? $order : null;
    }
}
