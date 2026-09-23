<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Offers\Api\OfferProviderInterface;
use Rokezzz\Offers\Api\OfferRequestProviderInterface;
use Rokezzz\Offers\Controller\Adminhtml\Offer as OfferAction;

class Edit extends OfferAction implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly OfferProviderInterface $offerProvider,
        private readonly OfferRequestProviderInterface $offerRequestProvider
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $offerId = (int) $this->getRequest()->getParam('entity_id');
        $requestId = (int) $this->getRequest()->getParam('request_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($offerId > 0) {
            $offer = $this->offerProvider->getById($offerId);

            if (is_null($offer)) {
                $this->messageManager->addErrorMessage(__('This offer no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $title = __('Edit Offer %1', $offer->getIncrementId());
        } elseif ($requestId > 0) {
            $offerRequest = $this->offerRequestProvider->getById($requestId);

            if (is_null($offerRequest)) {
                $this->messageManager->addErrorMessage(__('This offer request no longer exists.'));

                return $resultRedirect->setPath('rokezzz_offers/request/index');
            }

            $existingOffer = $this->offerProvider->getByRequestId($requestId);

            if (!is_null($existingOffer) && !is_null($existingOffer->getEntityId())) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $existingOffer->getEntityId()]);
            }

            $title = __('Create Offer for Request #%1', $requestId);
        } else {
            $this->messageManager->addErrorMessage(__('Unable to open the offer form.'));

            return $resultRedirect->setPath('*/*/');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rokezzz_Offers::offer');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
