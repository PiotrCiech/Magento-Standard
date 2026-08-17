<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Rokezzz\Invoice\Api\InvoicePdfGeneratorInterface;
use Rokezzz\Invoice\Model\Config;
use Rokezzz\Invoice\Model\Invoice\InvoiceKsefEligibilityChecker;

class DownloadPdf extends Action
{
    public const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

    public function __construct(
        Context $context,
        private readonly FileFactory $fileFactory,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly InvoicePdfGeneratorInterface $pdfGenerator,
        private readonly Config $config,
        private readonly InvoiceKsefEligibilityChecker $eligibilityChecker
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');

        try {
            $invoice = $this->invoiceRepository->get($invoiceId);

            if (!$this->config->isEnabled((int) $invoice->getStoreId())
                || !$this->eligibilityChecker->isKsefEligibleType($invoice)
            ) {
                throw new LocalizedException(__('Invoice PDF is not available for this document.'));
            }

            $pdf = $this->pdfGenerator->generate($invoice);

            return $this->fileFactory->create(
                sprintf('%s.pdf', $invoice->getIncrementId()),
                ['type' => 'string', 'value' => $pdf, 'rm' => false],
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('This invoice no longer exists.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
