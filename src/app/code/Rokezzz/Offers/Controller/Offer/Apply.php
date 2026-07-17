<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Offer;

use Throwable;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\OfferApplicatorInterface;

class Apply extends AbstractAccount implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly OfferApplicatorInterface $offerApplicator
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $offerId = (int) $this->getRequest()->getParam('entity_id');
        $customerId = (int) $this->customerSession->getCustomerId();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest || !$this->formKeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(
                __('The form key is invalid. Please refresh the page and try again.')
            );

            return $resultRedirect->setPath('rokezzz_offers/offer/view', ['entity_id' => $offerId]);
        }

        try {
            $this->offerApplicator->applyToCart($offerId, $customerId);
            $this->messageManager->addSuccessMessage(__('The offer has been added to your cart.'));

            return $resultRedirect->setPath('checkout/cart');
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (Throwable) {
            $this->messageManager->addErrorMessage(
                __('The offer could not be applied to your cart right now. Please try again later.')
            );
        }

        return $resultRedirect->setPath('rokezzz_offers/offer/view', ['entity_id' => $offerId]);
    }
}
