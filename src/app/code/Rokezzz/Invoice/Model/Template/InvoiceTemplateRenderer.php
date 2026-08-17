<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Template;

use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class InvoiceTemplateRenderer
{
    public function __construct(
        private readonly ModuleDirReader $moduleDirReader
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(string $templateSubdir, string $templateName, array $context, bool $autoescape = false): string
    {
        $templateDirectory = $this->moduleDirReader->getModuleDir(
            Dir::MODULE_VIEW_DIR,
            'Rokezzz_Invoice'
        ) . '/' . $templateSubdir;

        $loader = new FilesystemLoader($templateDirectory);
        $twig = new Environment($loader, [
            'autoescape' => $autoescape ? 'html' : false,
            'strict_variables' => false,
        ]);

        return $twig->render($templateName, $context);
    }
}
