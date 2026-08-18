<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Controller\Catalog;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Rokezzz\CategoryAdvance\Model\Config;
use Rokezzz\CategoryAdvance\Model\Pdf\CatalogPdfGenerator;

class Download implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Config $config,
        private readonly CatalogPdfGenerator $catalogPdfGenerator,
        private readonly RawFactory $rawFactory,
        private readonly RedirectFactory $redirectFactory,
        private readonly ManagerInterface $messageManager
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->config->isPdfCatalogEnabled()) {
            $this->messageManager->addErrorMessage(__('PDF catalog download is disabled.'));

            return $this->redirectFactory->create()->setRefererOrBaseUrl();
        }

        $categoryId = (int) $this->request->getParam('id');

        try {
            $pdfContent = $this->catalogPdfGenerator->generate($categoryId);
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());

            return $this->redirectFactory->create()->setRefererOrBaseUrl();
        }

        $fileName = sprintf('catalog-category-%d.pdf', $categoryId);
        $result = $this->rawFactory->create();
        $result->setHeader('Content-Type', 'application/pdf', true);
        $result->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $fileName . '"',
            true
        );
        $result->setContents($pdfContent);

        return $result;
    }
}
