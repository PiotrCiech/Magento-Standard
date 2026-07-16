<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Request;

use Throwable;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\QuoteRequestSubmissionManagementInterface;

class SubmitFromCart extends AbstractAccount implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly QuoteRequestSubmissionManagementInterface $quoteRequestSubmissionManagement
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest || !$this->formKeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(
                __('The form key is invalid. Please refresh the page and try again.')
            );

            return $resultRedirect->setPath('checkout/cart');
        }

        $customerNote = trim((string) $request->getParam('customer_note'));
        $customerId = (int) $this->customerSession->getCustomerId();

        try {
            $this->quoteRequestSubmissionManagement->submitFromCart($customerId, $customerNote);
            $this->messageManager->addSuccessMessage(__('Your quote request has been submitted.'));
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (Throwable) {
            $this->messageManager->addErrorMessage(
                __('Your quote request could not be submitted right now. Please try again later.')
            );
        }

        return $resultRedirect->setPath('rokezzz_offers/request/history');
    }
}
