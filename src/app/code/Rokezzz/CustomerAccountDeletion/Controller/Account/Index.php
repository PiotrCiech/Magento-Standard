<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Controller\Account;

use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements AccountInterface, HttpGetActionInterface
{
    public function __construct(
        private readonly Session $customerSession,
        private readonly PageFactory $pageFactory,
        private readonly RedirectFactory $resultRedirectFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('customer/account/login');
        }

        return $this->pageFactory->create();
    }
}
