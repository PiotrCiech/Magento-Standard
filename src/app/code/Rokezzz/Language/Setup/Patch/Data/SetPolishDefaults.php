<?php

declare(strict_types=1);

namespace Rokezzz\Language\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

final class SetPolishDefaults implements DataPatchInterface
{
    private const DEFAULT_SCOPE = 'default';
    private const DEFAULT_SCOPE_ID = 0;
    private const CONFIG_VALUES = [
        'general/country/default' => 'PL',
        'general/country/allow' => 'PL',
        'general/locale/code' => 'pl_PL',
        'general/locale/timezone' => 'Europe/Warsaw',
        'general/locale/weight_unit' => 'kgs',
        'currency/options/base' => 'PLN',
        'currency/options/default' => 'PLN',
        'currency/options/allow' => 'PLN',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigResource $configResource
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        foreach (self::CONFIG_VALUES as $path => $value) {
            $this->configResource->saveConfig(
                $path,
                $value,
                self::DEFAULT_SCOPE,
                self::DEFAULT_SCOPE_ID
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
        return [
            // Legacy class from removed Rokezzz_LocaleDefaults module.
            'Rokezzz\LocaleDefaults\Setup\Patch\Data\SetPolishDefaults',
        ];
    }
}
