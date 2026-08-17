<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Block\Adminhtml\Invoice;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\InvoiceInterface;
use Rokezzz\Invoice\Model\AdminRoutePaths;
use Rokezzz\Invoice\Model\Config;
use Rokezzz\Invoice\Model\Invoice\InvoiceKsefEligibilityChecker;

class PdfDownloadButton extends Template
{
    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly Config $config,
        private readonly InvoiceKsefEligibilityChecker $eligibilityChecker,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function canShow(): bool
    {
        $invoice = $this->getInvoice();

        return !is_null($invoice)
            && $this->config->isEnabled((int) $invoice->getStoreId())
            && $this->eligibilityChecker->isKsefEligibleType($invoice);
    }

    public function getDownloadUrl(): string
    {
        $invoice = $this->getInvoice();

        return $this->getUrl(AdminRoutePaths::INVOICE_DOWNLOAD_PDF, [
            'invoice_id' => $invoice?->getEntityId(),
        ]);
    }

    private function getInvoice(): ?InvoiceInterface
    {
        $invoice = $this->registry->registry('current_invoice');

        return $invoice instanceof InvoiceInterface ? $invoice : null;
    }
}
