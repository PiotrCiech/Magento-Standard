<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Request;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;
use Rokezzz\Offers\Api\OfferRequestManagementInterface;
use Rokezzz\Offers\Model\Data\OfferRequestItemDataCreator;

class Submit extends AbstractAccount implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly OfferRequestManagementInterface $offerRequestManagement,
        private readonly OfferRequestItemDataCreator $offerRequestItemDataCreator
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = (int) $this->customerSession->getCustomerId();
        $request = $this->getRequest();

        if (!$request instanceof HttpRequest || !$this->formKeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(
                __('The form key is invalid. Please refresh the page and try again.')
            );

            return $resultRedirect->setPath('rokezzz_offers/request/history');
        }

        $customerNote = trim((string) $request->getParam('customer_note'));
        $items = $this->buildRequestItems($request->getParam('items'));

        try {
            $this->offerRequestManagement->submit($customerId, $customerNote, $items);
            $this->messageManager->addSuccessMessage(__('Your quote request has been submitted.'));
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (\Throwable) {
            $this->messageManager->addErrorMessage(
                __('Your quote request could not be submitted right now. Please try again later.')
            );
        }

        return $resultRedirect->setPath('rokezzz_offers/request/history');
    }

    /**
     * @param mixed $rows
     *
     * @return OfferRequestItemDataInterface[]
     */
    private function buildRequestItems(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $items = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $itemData = $this->offerRequestItemDataCreator->create();
            $itemData->setProductId((int) ($row['product_id'] ?? 0));
            $itemData->setQty((float) ($row['qty'] ?? 0));
            $itemData->setCustomerNote(trim((string) ($row['customer_note'] ?? '')));
            $items[] = $itemData;
        }

        return $items;
    }
}
