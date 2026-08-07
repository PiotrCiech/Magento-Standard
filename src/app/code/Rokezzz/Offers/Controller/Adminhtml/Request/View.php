<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml\Request;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Controller\Adminhtml\Request as RequestAction;

class View extends RequestAction implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly OfferRequestProviderInterface $offerRequestProvider
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $requestId = (int) $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $offerRequest = $this->offerRequestProvider->getById($requestId);

        if (is_null($offerRequest)) {
            $this->messageManager->addErrorMessage(__('This offer request no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Offers::request');
        $resultPage->getConfig()->getTitle()->prepend(
            __('Offer Request #%1', $offerRequest->getEntityId())
        );

        return $resultPage;
    }
}
