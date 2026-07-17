<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Offer;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\Offers\Api\OfferProviderInterface;

class View extends AbstractAccount implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly CustomerSession $customerSession,
        private readonly OfferProviderInterface $offerProvider
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $offerId = (int) $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $offer = $this->offerProvider->getByIdForCustomer(
                $offerId,
                (int) $this->customerSession->getCustomerId()
            );
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('This offer no longer exists.'));

            return $resultRedirect->setPath('rokezzz_offers/offer/index');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Offer %1', $offer->getIncrementId()));

        return $resultPage;
    }
}
