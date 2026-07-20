<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Controller\Account;

use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Rokezzz\LoyaltyPoints\Model\Config;

final class Index implements AccountInterface, HttpGetActionInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly PageFactory $pageFactory,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly Config $config
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('customer/account/login');
        }

        if (!$this->config->isEnabled()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('customer/account');
        }

        return $this->pageFactory->create();
    }
}
