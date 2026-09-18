<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Tax\Model\Config as TaxConfig;
use Rokezzz\Tax\Model\Config\PriceDisplayModeSynchronizer;

final class SetGrossPriceDisplayPatch implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly PriceDisplayModeSynchronizer $priceDisplayModeSynchronizer
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

        $this->priceDisplayModeSynchronizer->sync(
            TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
            invalidateSearchIndex: false
        );

        $connection->endSetup();
    }
}
