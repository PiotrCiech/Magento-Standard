<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Backend\Model\Auth;

use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\Session\Config\ConfigInterface;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class RenewAdminSessionCookiePlugin
{
    public function __construct(
        private readonly RememberMeManager $rememberMeManager,
        private readonly ConfigInterface $sessionConfig
    ) {
    }

    public function aroundProlong(AdminSession $subject, callable $proceed): void
    {
        $proceed();
        $this->rememberMeManager->renewAdminSessionCookie($subject, $this->sessionConfig);
    }
}
