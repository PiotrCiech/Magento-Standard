<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Model\Invoice\LegalType;
use Rokezzz\Invoices\Model\Invoice\PaymentMethod;
use Rokezzz\Invoices\Model\Invoice\Unit;

class InvoiceXmlContextBuilder
{
    private const PAYMENT_TERM_DAYS = 14;

    public function __construct(
        private readonly SellerContextBuilder $sellerContextBuilder,
        private readonly BuyerContextBuilder $buyerContextBuilder,
        private readonly CorrectionContextBuilder $correctionContextBuilder,
        private readonly InvoiceLineBuilder $invoiceLineBuilder,
        private readonly VatLineFormatter $vatLineFormatter,
        private readonly PaymentMethodResolver $paymentMethodResolver,
        private readonly LegalTypeResolver $legalTypeResolver
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(InvoiceInterface $invoice): array
    {
        $storeId = (int) $invoice->getStoreId();
        $legalType = $this->legalTypeResolver->resolve($invoice);
        $paymentMethod = $this->paymentMethodResolver->resolve($invoice);
        $issuedAt = new \DateTimeImmutable((string) $invoice->getCreatedAt());
        $soldAt = new \DateTimeImmutable((string) $invoice->getOrder()?->getCreatedAt() ?: $invoice->getCreatedAt());
        $lines = $this->invoiceLineBuilder->build($invoice);

        $context = array_merge(
            $this->getDictionaryContext(),
            $this->sellerContextBuilder->build($storeId),
            $this->buyerContextBuilder->build($invoice),
            [
                'typeInvoice' => $legalType->value,
                'number' => (string) $invoice->getIncrementId(),
                'issuedAt' => $issuedAt,
                'soldAt' => $soldAt,
                'expiresAt' => $issuedAt->modify('+' . self::PAYMENT_TERM_DAYS . ' days'),
                'paymentMethod' => $paymentMethod->value,
                'comment' => (string) $invoice->getOrder()?->getCustomerNote(),
                'totalNet' => (float) $invoice->getSubtotal(),
                'totalVat' => (float) $invoice->getTaxAmount(),
                'totalGross' => (float) $invoice->getGrandTotal(),
                'items' => $this->formatLines($lines),
                'isInvoiceVatExempt' => (float) $invoice->getTaxAmount() <= 0.0,
                'vatExemptionBasis' => '',
            ],
            $this->correctionContextBuilder->build($invoice, $legalType)
        );

        return $context;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getDictionaryContext(): array
    {
        return [
            'InvoiceTypeDictionary' => [
                'correction' => LegalType::Correction->value,
                'standard' => LegalType::Standard->value,
                'proforma' => LegalType::Proforma->value,
                'advance' => LegalType::Advance->value,
            ],
            'InvoicePaymentMethodDictionary' => [
                'bankTransfer' => PaymentMethod::BankTransfer->value,
                'cash' => PaymentMethod::Cash->value,
                'paidInAdvance' => PaymentMethod::PaidInAdvance->value,
            ],
            'InvoiceUnitDictionary' => [
                'service' => Unit::Service->value,
                'item' => Unit::Item->value,
            ],
        ];
    }

    /**
     * @param \Rokezzz\Invoices\Model\Data\InvoiceLine[] $lines
     * @return array<int, array<string, string>>
     */
    private function formatLines(array $lines): array
    {
        $formatted = [];

        foreach ($lines as $line) {
            $formatted[] = $this->vatLineFormatter->format($line);
        }

        return $formatted;
    }
}
