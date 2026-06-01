<?php

declare(strict_types=1);

namespace Rokezzz\CheckoutAgreements\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DisableMultishippingByDefault implements DataPatchInterface
{
    private const MULTISHIPPING_ENABLED_CONFIG_PATH = 'multishipping/options/checkout_multiple';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigResource $configResource
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $this->configResource->saveConfig(
            self::MULTISHIPPING_ENABLED_CONFIG_PATH,
            '0',
            'default',
            0
        );

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
