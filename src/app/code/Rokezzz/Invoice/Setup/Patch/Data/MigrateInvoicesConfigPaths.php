<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

final class MigrateInvoicesConfigPaths implements DataPatchInterface
{
    private const OLD_PREFIX = 'rokezzz_invoices/';
    private const NEW_PREFIX = 'rokezzz_invoice/';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('core_config_data');
        $connection->startSetup();

        $select = $connection->select()
            ->from($table, ['config_id', 'scope', 'scope_id', 'path'])
            ->where('path LIKE ?', self::OLD_PREFIX . '%');

        foreach ($connection->fetchAll($select) as $row) {
            $newPath = self::NEW_PREFIX . substr((string) $row['path'], strlen(self::OLD_PREFIX));
            $existingId = $connection->fetchOne(
                $connection->select()
                    ->from($table, 'config_id')
                    ->where('scope = ?', $row['scope'])
                    ->where('scope_id = ?', $row['scope_id'])
                    ->where('path = ?', $newPath)
            );

            if ($existingId) {
                $connection->delete($table, ['config_id = ?' => $row['config_id']]);

                continue;
            }

            $connection->update(
                $table,
                ['path' => $newPath],
                ['config_id = ?' => $row['config_id']]
            );
        }

        $connection->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
