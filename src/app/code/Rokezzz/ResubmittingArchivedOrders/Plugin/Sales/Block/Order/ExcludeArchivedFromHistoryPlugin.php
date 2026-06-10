<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Plugin\Sales\Block\Order;

use Magento\Sales\Block\Order\History;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class ExcludeArchivedFromHistoryPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @param Collection|false $orders
     * @return Collection|false
     */
    public function afterGetOrders(History $subject, $orders)
    {
        unset($subject);

        if (!$this->config->isEnabled() || $orders === false) {
            return $orders;
        }

        $orders->addFieldToFilter(
            ['rokezzz_is_archived', 'rokezzz_is_archived'],
            [['null' => true], ['eq' => 0]]
        );

        return $orders;
    }
}
