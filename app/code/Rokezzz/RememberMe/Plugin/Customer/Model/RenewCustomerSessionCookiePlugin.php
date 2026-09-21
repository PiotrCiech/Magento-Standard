<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Customer\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Session\Config\ConfigInterface;
use Rokezzz\RememberMe\Model\RememberMeManager;

final class RenewCustomerSessionCookiePlugin
{
    public function __construct(
        private readonly RememberMeManager $rememberMeManager,
        private readonly ConfigInterface $sessionConfig
    ) {
    }

    public function afterStart(CustomerSession $subject, CustomerSession $result): CustomerSession
    {
        $this->rememberMeManager->renewCustomerSessionCookie($result, $this->sessionConfig);

        return $result;
    }
}
