<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Xml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoice\Api\InvoiceXmlFileWriterInterface;
use Rokezzz\Invoice\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoice\Model\Config;
use Rokezzz\Invoice\Model\Template\InvoiceTemplateRenderer;

class InvoiceXmlGenerator implements InvoiceXmlGeneratorInterface
{
    private const TEMPLATE_NAME = 'invoice.xml.twig';
    private const TEMPLATE_SUBDIR = 'ksef';

    public function __construct(
        private readonly Config $config,
        private readonly InvoiceXmlContextBuilder $contextBuilder,
        private readonly InvoiceTemplateRenderer $templateRenderer,
        private readonly InvoiceXmlFileWriterInterface $xmlFileWriter
    ) {
    }

    public function generate(InvoiceInterface $invoice): string
    {
        if (!$this->config->isEnabled((int) $invoice->getStoreId())) {
            throw new LocalizedException(__('Invoice generation is disabled.'));
        }

        $invoiceNumber = (string) $invoice->getIncrementId();
        $storeId = (int) $invoice->getStoreId();

        if ($this->xmlFileWriter->exists($invoiceNumber, $storeId)) {
            return $this->xmlFileWriter->read($invoiceNumber, $storeId);
        }

        $xmlContent = $this->templateRenderer->render(
            self::TEMPLATE_SUBDIR,
            self::TEMPLATE_NAME,
            $this->contextBuilder->build($invoice),
            false
        );

        if (trim($xmlContent) === '') {
            throw new LocalizedException(__('Generated invoice XML is empty.'));
        }

        $this->xmlFileWriter->write($invoiceNumber, $xmlContent, $storeId);

        return $xmlContent;
    }

    public function delete(InvoiceInterface $invoice): void
    {
        $this->xmlFileWriter->delete((string) $invoice->getIncrementId(), (int) $invoice->getStoreId());
    }
}
