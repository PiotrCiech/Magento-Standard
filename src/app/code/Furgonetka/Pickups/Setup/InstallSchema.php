<?php

namespace Furgonetka\Pickups\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();

        $columns = [
            'furgonetka_pickup_point' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'default' => null,
                'comment' => 'furgonetka_pickup_point',
            ],
        ];

        $table = 'quote';
        foreach ($columns as $columnName => $columnData) {
            $setup->getConnection()->addColumn($setup->getTable($table), $columnName, $columnData);
        }


        $table = 'sales_order';
        $columns['furgonetka_packages_numbers'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'default' => null,
            'comment' => 'furgonetka_packages_numbers',
        ];

        $columns['furgonetka_tracking_number'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'default' => null,
            'comment' => 'furgonetka_tracking_number',
        ];

        foreach ($columns as $columnName => $columnData) {
            $setup->getConnection()->addColumn($setup->getTable($table), $columnName, $columnData);
        }

        $setup->endSetup();
    }

}
