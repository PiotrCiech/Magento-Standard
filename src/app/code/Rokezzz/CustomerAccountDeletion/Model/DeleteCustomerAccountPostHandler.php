<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Rokezzz\CustomerAccountDeletion\Service\DeleteCustomerAccount;
use Rokezzz\CustomerAccountDeletion\Service\ValidateCustomerPassword;

class DeleteCustomerAccountPostHandler
{
    private const DELETE_ACCOUNT_PATH = 'customerdelete/account';

    public function __construct(
        private readonly Session $customerSession,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly ManagerInterface $messageManager,
        private readonly DeleteCustomerAccount $deleteCustomerAccount,
        private readonly ValidateCustomerPassword $validateCustomerPassword
    ) {
    }

    public function execute(): Redirect
    {
        $password = (string) $this->request->getParam('current_password');

        if ($password === '') {
            return $this->redirectWithError(__('Current password is required.'));
        }

        $customerId = (int) $this->customerSession->getCustomerId();

        if (!$this->validateCustomerPassword->execute($customerId, $password)) {
            return $this->redirectWithError(__('Invalid password.'));
        }

        $this->deleteCustomerAccount->execute($customerId);
        $this->messageManager->addSuccessMessage(__('Your account has been deleted.'));
        $this->customerSession->logout();

        return $this->resultRedirectFactory->create()->setPath('');
    }

    private function redirectWithError(Phrase|string $message): Redirect
    {
        $this->messageManager->addErrorMessage($message);

        return $this->resultRedirectFactory->create()->setPath(self::DELETE_ACCOUNT_PATH);
    }
}
