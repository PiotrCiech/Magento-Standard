<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Customer\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class ApplyRememberMeOnLoginPostPlugin
{
    public function __construct(
        private readonly RememberMeManager $rememberMeManager,
        private readonly CustomerSession $customerSession,
        private readonly ConfigInterface $sessionConfig,
        private readonly RequestInterface $request
    ) {
    }

    public function afterExecute(LoginPost $subject, ResultInterface $result): ResultInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $result;
        }

        $rememberMe = $this->rememberMeManager->isRememberMeCheckedForCustomer($this->request);
        $this->rememberMeManager->applyCustomerRememberMe(
            $this->customerSession,
            $this->sessionConfig,
            $rememberMe
        );

        return $result;
    }
}
