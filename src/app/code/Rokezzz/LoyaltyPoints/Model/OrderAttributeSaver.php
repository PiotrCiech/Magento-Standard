<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class OrderAttributeSaver
{
    public function __construct(
        private readonly OrderResource $orderResource
    ) {
    }

    public function saveAttribute(Order $order, string $attributeCode): void
    {
        $this->orderResource->saveAttribute($order, $attributeCode);
    }
}
