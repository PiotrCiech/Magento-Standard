<?php

declare(strict_types=1);

namespace Rokezzz\ContractWithdrawal\Controller\Order;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Rokezzz\ContractWithdrawal\Model\WithdrawalRequestManagement;

final class Submit implements HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly CustomerSession $customerSession,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly WithdrawalRequestManagement $withdrawalRequestManagement,
        private readonly MessageManager $messageManager
    ) {
    }

    public function execute(): ResultInterface
    {
        $customerId = (int) $this->customerSession->getCustomerId();
        $orderId = (int) $this->request->getParam('order_id');
        $statementDetails = trim((string) $this->request->getParam('statement_details'));

        $redirect = $this->createOrderRedirect($orderId);

        if ($customerId === 0) {
            return $redirect->setPath('customer/account/login');
        }

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('The form key is invalid. Please refresh the page and try again.'));

            return $redirect;
        }

        try {
            $order = $this->orderRepository->get($orderId);
            $this->withdrawalRequestManagement->submit($order, $customerId, $statementDetails);
            $this->messageManager->addSuccessMessage(
                __('Your withdrawal request has been submitted successfully.')
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable) {
            $this->messageManager->addErrorMessage(
                __('The withdrawal request could not be submitted right now.')
            );
        }

        return $redirect;
    }

    private function createOrderRedirect(int $orderId): Redirect
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($orderId > 0) {
            return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        return $redirect->setPath('sales/order/history');
    }
}
