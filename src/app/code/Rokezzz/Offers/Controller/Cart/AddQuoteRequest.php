<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Cart;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\QuoteRequestCartManagementInterface;

class AddQuoteRequest extends AbstractAccount implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly QuoteRequestCartManagementInterface $quoteRequestCartManagement
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

        $productId = (int) $request->getParam('product_id');
        $qty = (float) $request->getParam('qty', 1);
        $customerId = (int) $this->customerSession->getCustomerId();

        try {
            $this->quoteRequestCartManagement->addToCart($customerId, $productId, $qty);
            $this->messageManager->addSuccessMessage(__('The product has been added to your quote request cart.'));
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (\Throwable) {
            $this->messageManager->addErrorMessage(
                __('The product could not be added to your quote request cart right now.')
            );
        }

        return $this->resolveRedirect($resultRedirect, $productId);
    }

    private function resolveRedirect(Redirect $resultRedirect, int $productId): Redirect
    {
        $returnUrl = trim((string) $this->getRequest()->getParam('return_url'));

        if ($returnUrl !== '') {
            return $resultRedirect->setUrl($returnUrl);
        }

        if ($productId > 0) {
            return $resultRedirect->setPath('catalog/product/view', ['id' => $productId]);
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
