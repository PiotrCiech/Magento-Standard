<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Api;

use Magento\Sales\Model\Reorder\Data\ReorderOutput;

interface ResubmitArchivedOrderInterface
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, int $orderId): ReorderOutput;
}
