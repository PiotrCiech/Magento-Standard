<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

final class SetPolishTaxDestinationPatch implements DataPatchInterface
{
    private const CONFIG_TO_INSTALL = [
        'tax/defaults/country' => 'PL',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly WriterInterface $configWriter
    ) {
    }

    public static function getDependencies(): array
    {
        return [AddTaxRulePatch::class];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        foreach (self::CONFIG_TO_INSTALL as $path => $value) {
            $this->configWriter->save($path, $value);
        }

        $connection->endSetup();
    }
}
