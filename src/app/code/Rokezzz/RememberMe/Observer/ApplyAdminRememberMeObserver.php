<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Observer;

use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class ApplyAdminRememberMeObserver implements ObserverInterface
{
    public function __construct(
        private readonly RememberMeManager $rememberMeManager,
        private readonly AdminSession $adminSession,
        private readonly ConfigInterface $sessionConfig,
        private readonly RequestInterface $request
    ) {
    }

    public function execute(Observer $observer): void
    {
        $rememberMe = $this->rememberMeManager->isRememberMeCheckedForAdmin($this->request);
        $this->rememberMeManager->applyAdminRememberMe(
            $this->adminSession,
            $this->sessionConfig,
            $rememberMe
        );
    }
}
