<?php

declare(strict_types=1);

namespace Rokezzz\Core\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Theme\Model\Theme\Registration as ThemeRegistration;

/**
 * Assigns Rokezzz/default as the storefront theme after install.
 * theme_path in DB is "Rokezzz/default"; use getThemeByFullPath() for area+path.
 */
final class AssignDefaultTheme implements DataPatchInterface
{
    private const DEFAULT_SCOPE = 'default';
    private const DEFAULT_SCOPE_ID = 0;
    private const THEME_FULL_PATH = 'frontend/Rokezzz/default';
    private const CONFIG_PATH = 'design/theme/theme_id';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigResource $configResource,
        private readonly ThemeCollectionFactory $themeCollectionFactory,
        private readonly ThemeRegistration $themeRegistration
    ) {
    }

    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $this->themeRegistration->register();

        $theme = $this->themeCollectionFactory->create()
            ->getThemeByFullPath(self::THEME_FULL_PATH);

        if ($theme && $theme->getId()) {
            $this->configResource->saveConfig(
                self::CONFIG_PATH,
                (string) $theme->getId(),
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
        return [];
    }
}
