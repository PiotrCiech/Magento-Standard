<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Rokezzz\Invoices\Api\InvoiceXmlGeneratorInterface;
use Rokezzz\Invoices\Model\Invoice\InvoiceKsefEligibilityChecker;
use Rokezzz\Invoices\Model\Invoice\InvoiceKsefStatusWriter;
use Rokezzz\Invoices\Model\Invoice\KsefUpoStatus;
use Rokezzz\Invoices\Model\Invoice\LegalType;

class QueueInvoiceForKsefSubmission implements ObserverInterface
{
    public function __construct(
        private readonly InvoiceKsefEligibilityChecker $eligibilityChecker,
        private readonly InvoiceXmlGeneratorInterface $invoiceXmlGenerator,
        private readonly InvoiceKsefStatusWriter $statusWriter,
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {
    }

    public function execute(Observer $observer): void
    {
        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice instanceof InvoiceInterface || !$invoice->getEntityId()) {
            return;
        }

        if ((string) $invoice->getData('rokezzz_ksef_upo_status') !== '') {
            return;
        }

        if ((string) $invoice->getData('rokezzz_invoice_legal_type') === '') {
            $invoice->setData('rokezzz_invoice_legal_type', LegalType::Standard->value);
            $this->invoiceRepository->save($invoice);
        }

        if (!$this->eligibilityChecker->shouldQueueForKsef($invoice)) {
            return;
        }

        $this->invoiceXmlGenerator->generate($invoice);
        $this->statusWriter->update($invoice, KsefUpoStatus::Todo);
    }
}
