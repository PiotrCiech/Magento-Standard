<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Pdf;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Model\Invoice\LegalType;
use Rokezzz\Invoices\Model\Invoice\PaymentMethod;
use Rokezzz\Invoices\Model\Xml\InvoiceXmlContextBuilder;
use Rokezzz\Settings\Api\CompanySignatureProviderInterface;
use Rokezzz\Settings\Api\SellerDataProviderInterface;

class InvoicePdfContextBuilder
{
    public function __construct(
        private readonly InvoiceXmlContextBuilder $invoiceContextBuilder,
        private readonly CompanySignatureProviderInterface $companySignatureProvider,
        private readonly SellerDataProviderInterface $sellerDataProvider
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(InvoiceInterface $invoice): array
    {
        $storeId = (int) $invoice->getStoreId();
        $context = $this->invoiceContextBuilder->build($invoice);
        $seller = $this->sellerDataProvider->get($storeId);

        $context['sellerLogoUrl'] = $seller->getLogoUrl();
        $context['companySignatureHtml'] = $this->companySignatureProvider->getHtml($storeId);
        $context['invoiceTypeLabel'] = $this->resolveInvoiceTypeLabel((string) $context['typeInvoice']);
        $context['paymentMethodLabel'] = $this->resolvePaymentMethodLabel((string) $context['paymentMethod']);
        $context['ksefNumber'] = (string) $invoice->getData('rokezzz_ksef_number');

        if ($context['typeInvoice'] === LegalType::Correction->value) {
            $context['items'] = $context['correctionItems'] ?? [];
        }

        return $context;
    }

    private function resolveInvoiceTypeLabel(string $type): string
    {
        return match ($type) {
            LegalType::Correction->value => 'Faktura korygująca',
            LegalType::Advance->value => 'Faktura zaliczkowa',
            LegalType::Proforma->value => 'Faktura proforma',
            default => 'Faktura VAT',
        };
    }

    private function resolvePaymentMethodLabel(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            PaymentMethod::Cash->value => 'Gotówka',
            PaymentMethod::PaidInAdvance->value => 'Zapłacono z góry',
            default => 'Przelew bankowy',
        };
    }
}
