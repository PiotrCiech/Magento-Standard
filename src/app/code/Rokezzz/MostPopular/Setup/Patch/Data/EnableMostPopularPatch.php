<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class EnableMostPopularPatch implements DataPatchInterface
{
    private const CONFIG_TO_INSTALL = [
        'rokezzz_mostpopular/general/enabled' => '1',
        'rokezzz_mostpopular/general/products_count' => '8',
        'rokezzz_mostpopular/general/period' => 'month',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly WriterInterface $configWriter
    ) {
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

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
