<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Helper\Reorder as ReorderHelper;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class ArchivedOrderActions implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ReorderHelper $reorderHelper
    ) {
    }

    public function canResubmit(OrderInterface $order): bool
    {
        if (!$this->config->isEnabled()) {
            return false;
        }

        return $this->reorderHelper->canReorder((int) $order->getEntityId());
    }
}
