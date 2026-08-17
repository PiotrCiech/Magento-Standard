<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Pdf;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoice\Api\InvoicePdfFileWriterInterface;
use Rokezzz\Invoice\Api\InvoicePdfGeneratorInterface;
use Rokezzz\Invoice\Model\Config;
use Rokezzz\Invoice\Model\Template\InvoiceTemplateRenderer;

class InvoicePdfGenerator implements InvoicePdfGeneratorInterface
{
    private const TEMPLATE_NAME = 'invoice.html.twig';
    private const TEMPLATE_SUBDIR = 'pdf';

    public function __construct(
        private readonly Config $config,
        private readonly InvoicePdfContextBuilder $contextBuilder,
        private readonly InvoiceTemplateRenderer $templateRenderer,
        private readonly InvoicePdfConverter $pdfConverter,
        private readonly InvoicePdfFileWriterInterface $pdfFileWriter
    ) {
    }

    public function generate(InvoiceInterface $invoice): string
    {
        if (!$this->config->isEnabled((int) $invoice->getStoreId())) {
            throw new LocalizedException(__('Invoice PDF generation is disabled.'));
        }

        $invoiceNumber = (string) $invoice->getIncrementId();
        $storeId = (int) $invoice->getStoreId();

        if ($this->pdfFileWriter->exists($invoiceNumber, $storeId)) {
            return $this->pdfFileWriter->read($invoiceNumber, $storeId);
        }

        $html = $this->templateRenderer->render(
            self::TEMPLATE_SUBDIR,
            self::TEMPLATE_NAME,
            $this->contextBuilder->build($invoice),
            true
        );

        if (trim($html) === '') {
            throw new LocalizedException(__('Generated invoice HTML is empty.'));
        }

        $pdfContent = $this->pdfConverter->convert($html);

        if ($pdfContent === '') {
            throw new LocalizedException(__('Generated invoice PDF is empty.'));
        }

        $this->pdfFileWriter->write($invoiceNumber, $pdfContent, $storeId);

        return $pdfContent;
    }

    public function delete(InvoiceInterface $invoice): void
    {
        $this->pdfFileWriter->delete((string) $invoice->getIncrementId(), (int) $invoice->getStoreId());
    }
}
