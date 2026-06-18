<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class InvoiceTwigRenderer
{
    private const TEMPLATE_NAME = 'invoice.xml.twig';
    private const TEMPLATE_SUBDIR = 'ksef';

    public function __construct(
        private readonly ModuleDirReader $moduleDirReader
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(array $context): string
    {
        $templateDirectory = $this->moduleDirReader->getModuleDir(
            Dir::MODULE_VIEW_DIR,
            'Rokezzz_Invoices'
        ) . '/' . self::TEMPLATE_SUBDIR;

        $loader = new FilesystemLoader($templateDirectory);
        $twig = new Environment($loader, [
            'autoescape' => false,
            'strict_variables' => false,
        ]);

        return $twig->render(self::TEMPLATE_NAME, $context);
    }
}
