<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Model\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class CatalogPdfGenerator
{
    private const TEMPLATE_FILE = 'catalog.twig';
    private const STYLES_FILE = 'catalog.css';

    public function __construct(
        private readonly ModuleDirReader $moduleDirReader,
        private readonly CatalogPdfViewDataBuilder $catalogPdfViewDataBuilder
    ) {
    }

    public function generate(int $categoryId): string
    {
        $html = $this->renderHtml($categoryId);
        $dompdf = new Dompdf($this->createOptions());
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }

    private function renderHtml(int $categoryId): string
    {
        $pdfDirectory = $this->moduleDirReader->getModuleDir(
            Dir::MODULE_VIEW_DIR,
            'Rokezzz_CategoryAdvance'
        ) . '/pdf';

        $loader = new FilesystemLoader($pdfDirectory . '/templates');
        $twig = new Environment($loader, [
            'autoescape' => 'html',
            'strict_variables' => true,
        ]);

        $viewData = $this->catalogPdfViewDataBuilder->build($categoryId);
        $viewData['styles'] = (string) file_get_contents(
            $pdfDirectory . '/css/' . self::STYLES_FILE
        );

        return $twig->render(self::TEMPLATE_FILE, $viewData);
    }

    private function createOptions(): Options
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        return $options;
    }
}
