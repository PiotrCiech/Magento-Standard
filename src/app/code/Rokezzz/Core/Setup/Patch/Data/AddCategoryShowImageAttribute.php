<?php
declare(strict_types=1);

namespace Rokezzz\Core\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCategoryShowImageAttribute implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CategorySetupFactory $categorySetupFactory
    ) {}

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $categorySetup->addAttribute(
            Category::ENTITY,
            'show_image_on_homepage',
            [
                'type' => 'int',
                'label' => 'Pokaż zdjęcie na stronie głównej',
                'input' => 'boolean',
                'source' => Boolean::class,
                'visible' => true,
                'default' => '1',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Display Settings',
            ]
        );

        return $this;
    }
}