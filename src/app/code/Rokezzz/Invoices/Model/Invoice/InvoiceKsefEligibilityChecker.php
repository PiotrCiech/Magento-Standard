<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoices\Model\Config;
use Rokezzz\Invoices\Model\Xml\LegalTypeResolver;

final class InvoiceKsefEligibilityChecker
{
    public function __construct(
        private readonly Config $config,
        private readonly LegalTypeResolver $legalTypeResolver
    ) {
    }

    public function isModuleEnabled(InvoiceInterface $invoice): bool
    {
        return $this->config->isEnabled((int) $invoice->getStoreId());
    }

    public function isKsefSubmissionEnabled(InvoiceInterface $invoice): bool
    {
        return $this->config->isKsefEnabled((int) $invoice->getStoreId());
    }

    public function isKsefEligibleType(InvoiceInterface $invoice): bool
    {
        $legalType = $this->legalTypeResolver->resolve($invoice);

        return in_array($legalType->value, LegalType::ksefEligible(), true);
    }

    public function isIssuedOnOrAfterKsefStartDate(InvoiceInterface $invoice): bool
    {
        $storeId = (int) $invoice->getStoreId();
        $startDate = $this->config->getKsefStartDate($storeId);
        $issuedAt = substr((string) $invoice->getCreatedAt(), 0, 10);

        return $issuedAt >= $startDate;
    }

    public function shouldQueueForKsef(InvoiceInterface $invoice): bool
    {
        if (!$this->isModuleEnabled($invoice)) {
            return false;
        }

        if (!$this->isKsefSubmissionEnabled($invoice)) {
            return false;
        }

        if (!$this->isKsefEligibleType($invoice)) {
            return false;
        }

        return $this->isIssuedOnOrAfterKsefStartDate($invoice);
    }

    public function shouldSubmitToKsef(InvoiceInterface $invoice): bool
    {
        if (!$this->shouldQueueForKsef($invoice)) {
            return false;
        }

        return (string) $invoice->getData('rokezzz_ksef_upo_status') === KsefUpoStatus::Todo->value;
    }
}
