<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class OrderArchive
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * @param string[] $statuses
     */
    public function archiveOrdersCreatedBefore(string $createdBefore, array $statuses): int
    {
        if ($statuses === []) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('sales_order');

        return (int) $connection->update(
            $table,
            ['rokezzz_is_archived' => 1],
            [
                'rokezzz_is_archived = ?' => 0,
                'created_at < ?' => $createdBefore,
                'status IN (?)' => $statuses,
            ]
        );
    }
}
