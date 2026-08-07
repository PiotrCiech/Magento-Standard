<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Rokezzz\CustomerContact\Model\AssignedAdminAttribute;
use Rokezzz\CustomerContact\Model\CustomerAttribute\AssignedAdminSource;

final class AddAssignedAdminUserAttribute implements DataPatchInterface
{
    private const USED_IN_FORMS = [
        'adminhtml_customer',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetup $customerSetup
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $this->customerSetup->addAttribute(
            Customer::ENTITY,
            AssignedAdminAttribute::CODE,
            [
                'type' => 'int',
                'label' => 'Assigned Admin User',
                'input' => 'select',
                'source' => AssignedAdminSource::class,
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'system' => 0,
                'group' => 'General',
                'position' => 999,
                'sort_order' => 999,
            ]
        );

        $attribute = $this->customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            AssignedAdminAttribute::CODE
        );
        $attribute->setData('used_in_forms', self::USED_IN_FORMS);
        $attribute->save();

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
