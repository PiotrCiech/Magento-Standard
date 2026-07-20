<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\OfferPdfGeneratorInterface;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class OfferPdfGenerator implements OfferPdfGeneratorInterface
{
    private const TEMPLATE_FILE = 'offer.twig';

    public function __construct(
        private readonly ModuleDirReader $moduleDirReader,
        private readonly OfferPdfViewDataBuilder $offerPdfViewDataBuilder
    ) {
    }

    public function generate(OfferInterface $offer): string
    {
        try {
            $html = $this->renderHtml($offer);
            $dompdf = new Dompdf($this->createOptions());
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return (string) $dompdf->output();
        } catch (LocalizedException $localizedException) {
            throw $localizedException;
        } catch (Throwable $throwable) {
            throw new LocalizedException(
                __('The offer PDF could not be generated right now.'),
                $throwable
            );
        }
    }

    /**
     * @throws LocalizedException
     */
    private function renderHtml(OfferInterface $offer): string
    {
        $templateDirectory = $this->moduleDirReader->getModuleDir(
            Dir::MODULE_VIEW_DIR,
            'Rokezzz_Offers'
        ) . '/pdf/templates';

        $loader = new FilesystemLoader($templateDirectory);
        $twig = new Environment($loader, [
            'autoescape' => 'html',
            'strict_variables' => true,
        ]);

        return $twig->render(self::TEMPLATE_FILE, $this->offerPdfViewDataBuilder->build($offer));
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
