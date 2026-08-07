<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Rokezzz\CustomerContact\Model\AssignedAdminAttribute;

final class AddAssignedAdminUserAttributeToAttributeSet implements DataPatchInterface
{
    private const ATTRIBUTE_GROUP = 'General';
    private const SORT_ORDER = 999;

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetup $customerSetup
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $attributeId = (int) $this->customerSetup->getAttributeId(
            Customer::ENTITY,
            AssignedAdminAttribute::CODE
        );

        if ($attributeId <= 0) {
            $connection->endSetup();

            return;
        }

        $attributeSetTable = $this->moduleDataSetup->getTable('eav_attribute_set');
        $entityTypeId = (int) $this->customerSetup->getEntityTypeId(Customer::ENTITY);

        $attributeSetIds = $connection->fetchCol(
            $connection->select()
                ->from($attributeSetTable, ['attribute_set_id'])
                ->where('entity_type_id = ?', $entityTypeId)
        );

        foreach ($attributeSetIds as $attributeSetId) {
            $this->customerSetup->addAttributeToSet(
                Customer::ENTITY,
                (int) $attributeSetId,
                self::ATTRIBUTE_GROUP,
                AssignedAdminAttribute::CODE,
                self::SORT_ORDER
            );
        }

        $connection->endSetup();
    }

    public static function getDependencies(): array
    {
        return [AddAssignedAdminUserAttribute::class];
    }

    public function getAliases(): array
    {
        return [];
    }
}
