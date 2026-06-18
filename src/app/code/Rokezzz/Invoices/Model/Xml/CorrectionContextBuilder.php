<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Rokezzz\Invoices\Model\Data\InvoiceLine;
use Rokezzz\Invoices\Model\Invoice\LegalType;

class CorrectionContextBuilder
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly InvoiceLineBuilder $invoiceLineBuilder,
        private readonly VatLineFormatter $vatLineFormatter
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(InvoiceInterface $invoice, LegalType $legalType): array
    {
        if ($legalType !== LegalType::Correction) {
            return [];
        }

        $correctedInvoiceId = (int) $invoice->getData('rokezzz_corrected_invoice_id');
        if ($correctedInvoiceId <= 0) {
            throw new LocalizedException(__('Corrected invoice is required for correction invoices.'));
        }

        $correctedInvoice = $this->invoiceRepository->get($correctedInvoiceId);
        $itemsBefore = $this->invoiceLineBuilder->build($correctedInvoice);
        $itemsAfter = $this->invoiceLineBuilder->build($invoice);

        return [
            'correctedInvoiceNumber' => (string) $correctedInvoice->getIncrementId(),
            'correctedInvoiceIssuedAt' => new \DateTimeImmutable((string) $correctedInvoice->getCreatedAt()),
            'correctedInvoiceKsefNumber' => (string) $correctedInvoice->getData('rokezzz_ksef_number'),
            'correctedInvoiceSoldAt' => new \DateTimeImmutable(
                (string) ($correctedInvoice->getOrder()?->getCreatedAt() ?: $correctedInvoice->getCreatedAt())
            ),
            'correctedInvoiceTotalNet' => (float) $invoice->getSubtotal(),
            'correctedInvoiceTotalVat' => (float) $invoice->getTaxAmount(),
            'correctedInvoiceTotalGross' => (float) $invoice->getGrandTotal(),
            'totalNetBeforeCorrection' => (float) $correctedInvoice->getSubtotal(),
            'totalVatBeforeCorrection' => (float) $correctedInvoice->getTaxAmount(),
            'totalGrossBeforeCorrection' => (float) $correctedInvoice->getGrandTotal(),
            'correctionItems' => $this->buildCorrectionItems($itemsAfter, $itemsBefore),
        ];
    }

    /**
     * @param \Rokezzz\Invoices\Model\Data\InvoiceLine[] $itemsAfter
     * @param \Rokezzz\Invoices\Model\Data\InvoiceLine[] $itemsBefore
     * @return array<int, array<string, string>>
     */
    private function buildCorrectionItems(array $itemsAfter, array $itemsBefore): array
    {
        $correctionItems = [];

        foreach ($itemsAfter as $index => $afterLine) {
            $beforeLine = $itemsBefore[$index] ?? null;
            $beforeNet = $beforeLine?->getTotalNet() ?? 0.0;
            $beforeVat = $beforeLine?->getTotalVat() ?? 0.0;

            $diffLine = new InvoiceLine(
                name: $afterLine->getName(),
                unit: $afterLine->getUnit(),
                quantity: $afterLine->getQuantity(),
                totalNet: $afterLine->getTotalNet() - $beforeNet,
                totalVat: $afterLine->getTotalVat() - $beforeVat,
                vatRate: $afterLine->getVatRate(),
                isVatExempt: $afterLine->isVatExempt()
            );

            $correctionItems[] = $this->vatLineFormatter->format($diffLine);
        }

        return $correctionItems;
    }
}
