<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Sql\Expression;

class PriceHistory
{
    private const TABLE_NAME = 'rokezzz_omnibus_price_history';

    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    public function insert(int $productId, int $storeId, float $price): void
    {
        $this->getConnection()->insert(
            $this->getTableName(),
            [
                'product_id' => $productId,
                'store_id' => $storeId,
                'price' => round($price, 4),
            ]
        );
    }

    public function getMinimumPriceAbove(int $productId, int $storeId, int $days, float $currentPrice): ?float
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTableName(), ['min_price' => new Expression('MIN(price)')])
            ->where('product_id = ?', $productId)
            ->where('store_id = ?', $storeId)
            ->where('recorded_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL ? DAY)', $days)
            ->where('price > ?', round($currentPrice, 4) + 0.0001);

        $value = $connection->fetchOne($select);

        if ($value === false || $value === null) {
            return null;
        }

        return (float) $value;
    }

    public function deleteOlderThan(int $days): int
    {
        $connection = $this->getConnection();
        $condition = $connection->quoteInto(
            'recorded_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL ? DAY)',
            (int) $days
        );

        return (int) $connection->delete($this->getTableName(), $condition);
    }

    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }

    private function getTableName(): string
    {
        return $this->resourceConnection->getTableName(self::TABLE_NAME);
    }
}
