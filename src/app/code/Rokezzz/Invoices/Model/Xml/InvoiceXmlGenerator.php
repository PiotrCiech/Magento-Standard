<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Api\InvoiceXmlFileWriterInterface;
use Rokezzz\Invoices\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoices\Model\Config;

class InvoiceXmlGenerator implements InvoiceXmlGeneratorInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly InvoiceXmlContextBuilder $contextBuilder,
        private readonly InvoiceTwigRenderer $twigRenderer,
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

        $xmlContent = $this->twigRenderer->render(
            $this->contextBuilder->build($invoice)
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
