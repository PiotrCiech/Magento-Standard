<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class InvoicePdfConverter
{
    public function convert(string $html): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
