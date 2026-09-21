<?php

declare(strict_types=1);

namespace Rokezzz\InPost\Model;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\ModuleListInterface;

final class IntegrationStatus
{
    private const OFFICIAL_MODULE_NAME = 'Smartmage_Inpost';

    public function __construct(
        private readonly ModuleManager $moduleManager,
        private readonly ModuleListInterface $moduleList
    ) {
    }

    public function isOfficialModuleInstalled(): bool
    {
        return $this->moduleList->has(self::OFFICIAL_MODULE_NAME);
    }

    public function isOfficialModuleEnabled(): bool
    {
        return $this->moduleManager->isEnabled(self::OFFICIAL_MODULE_NAME);
    }
}
