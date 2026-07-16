<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml\Offer;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\OfferManagementInterface;
use Rokezzz\Offers\Controller\Adminhtml\Offer as OfferAction;

class Send extends OfferAction implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly OfferManagementInterface $offerManagement
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $offerId = (int) $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $offer = $this->offerManagement->markAsOffered($offerId);
            $this->messageManager->addSuccessMessage(__('The offer has been sent to the customer.'));
            $resultRedirect->setPath('*/*/edit', ['entity_id' => $offer->getEntityId()]);
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $resultRedirect->setPath('*/*/edit', ['entity_id' => $offerId]);
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('The offer could not be sent right now. Please try again later.')
            );
            $resultRedirect->setPath('*/*/edit', ['entity_id' => $offerId]);
        }

        return $resultRedirect;
    }
}
