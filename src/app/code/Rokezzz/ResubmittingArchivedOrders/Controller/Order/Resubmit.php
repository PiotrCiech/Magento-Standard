<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\Controller\Order;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\Reorder\Data\Error;
use Rokezzz\ResubmittingArchivedOrders\Api\ResubmitArchivedOrderInterface;

class Resubmit implements HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly CustomerSession $customerSession,
        private readonly ResubmitArchivedOrderInterface $resubmitArchivedOrder,
        private readonly MessageManager $messageManager
    ) {
    }

    public function execute(): ResultInterface
    {
        $customerId = (int) $this->customerSession->getCustomerId();
        $orderId = (int) $this->request->getParam('order_id');

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($customerId === 0) {
            return $redirect->setPath('customer/account/login');
        }

        try {
            $output = $this->resubmitArchivedOrder->execute($customerId, $orderId);
            $this->addReorderMessages($output->getErrors());
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $redirect->setPath('checkout/cart');
    }

    /**
     * @param Error[] $errors
     */
    private function addReorderMessages(array $errors): void
    {
        if ($errors === []) {
            $this->messageManager->addSuccessMessage(__('Products were added to your shopping cart.'));
            return;
        }

        foreach ($errors as $error) {
            $this->messageManager->addNoticeMessage($error->getMessage());
        }

        $this->messageManager->addSuccessMessage(
            __('Some products from the archived order were added to your shopping cart.')
        );
    }
}
