<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;

class PendingKsefInvoiceProvider
{
    public function __construct(
        private readonly CollectionFactory $invoiceCollectionFactory,
        private readonly InvoiceKsefEligibilityChecker $eligibilityChecker
    ) {
    }

    /**
     * @return InvoiceInterface[]
     */
    public function getPendingInvoices(): array
    {
        /** @var Collection $collection */
        $collection = $this->invoiceCollectionFactory->create();
        $collection->addFieldToFilter('rokezzz_ksef_upo_status', ['eq' => KsefUpoStatus::Todo->value]);
        $collection->setOrder('created_at', 'ASC');

        $pendingInvoices = [];

        foreach ($collection as $invoice) {
            if (!$invoice instanceof InvoiceInterface) {
                continue;
            }

            if (!$this->eligibilityChecker->shouldSubmitToKsef($invoice)) {
                continue;
            }

            $pendingInvoices[] = $invoice;
        }

        return $pendingInvoices;
    }
}
