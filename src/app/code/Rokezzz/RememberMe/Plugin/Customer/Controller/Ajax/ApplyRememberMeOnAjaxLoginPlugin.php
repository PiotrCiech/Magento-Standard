<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Customer\Controller\Ajax;

use Magento\Customer\Controller\Ajax\Login;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Session\Config\ConfigInterface;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class ApplyRememberMeOnAjaxLoginPlugin
{
    public function __construct(
        private readonly RememberMeManager $rememberMeManager,
        private readonly CustomerSession $customerSession,
        private readonly ConfigInterface $sessionConfig,
        private readonly JsonHelper $jsonHelper
    ) {
    }

    public function afterExecute(Login $subject, ResultInterface $result): ResultInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $result;
        }

        $rememberMe = $this->isRememberMeChecked($subject);
        $this->rememberMeManager->applyCustomerRememberMe(
            $this->customerSession,
            $this->sessionConfig,
            $rememberMe
        );

        return $result;
    }

    private function isRememberMeChecked(Login $subject): bool
    {
        $request = $subject->getRequest();
        if (!$request instanceof HttpRequest) {
            return false;
        }

        if ($request->getParam('remember_me')) {
            return true;
        }

        $content = $request->getContent();
        if ($content === '') {
            return false;
        }

        try {
            $credentials = $this->jsonHelper->jsonDecode($content);
        } catch (\Exception) {
            return false;
        }

        return !empty($credentials['remember_me']);
    }
}
