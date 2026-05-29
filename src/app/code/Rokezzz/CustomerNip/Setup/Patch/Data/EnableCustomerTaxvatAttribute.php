<?php

declare(strict_types=1);

namespace Rokezzz\CustomerNip\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class EnableCustomerTaxvatAttribute implements DataPatchInterface
{
    private const ATTRIBUTE_CODE = 'taxvat';

    private const USED_IN_FORMS = [
        'adminhtml_customer',
        'customer_account_create',
        'customer_account_edit',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $attributeId = $this->getCustomerAttributeId(self::ATTRIBUTE_CODE);
        if ($attributeId === false) {
            $connection->endSetup();

            return;
        }

        $connection->update(
            $this->moduleDataSetup->getTable('customer_eav_attribute'),
            ['is_visible' => 1],
            ['attribute_id = ?' => $attributeId]
        );

        $formAttributeTable = $this->moduleDataSetup->getTable('customer_form_attribute');
        foreach (self::USED_IN_FORMS as $formCode) {
            $connection->insertOnDuplicate(
                $formAttributeTable,
                [
                    'form_code' => $formCode,
                    'attribute_id' => $attributeId,
                ],
                ['form_code']
            );
        }

        $connection->endSetup();
    }

    /**
     * @return int|false
     */
    private function getCustomerAttributeId(string $attributeCode): int|false
    {
        $connection = $this->moduleDataSetup->getConnection();

        $select = $connection->select()
            ->from(['ea' => $this->moduleDataSetup->getTable('eav_attribute')], ['attribute_id'])
            ->join(
                ['et' => $this->moduleDataSetup->getTable('eav_entity_type')],
                'ea.entity_type_id = et.entity_type_id',
                []
            )
            ->where('et.entity_type_code = ?', Customer::ENTITY)
            ->where('ea.attribute_code = ?', $attributeCode);

        $attributeId = $connection->fetchOne($select);

        return $attributeId !== false ? (int) $attributeId : false;
    }

    public static function getDependencies(): array
    {
        return [EnableCustomerTaxvatField::class];
    }

    public function getAliases(): array
    {
        return [];
    }
}
