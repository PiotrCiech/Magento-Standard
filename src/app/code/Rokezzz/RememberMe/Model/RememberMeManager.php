<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Model;

use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

final class RememberMeManager
{
    public const SESSION_FLAG = 'rokezzz_remember_me';

    public function __construct(
        private readonly Config $config,
        private readonly CookieManagerInterface $cookieManager,
        private readonly CookieMetadataFactory $cookieMetadataFactory,
        private readonly AdminRememberMeFlag $adminRememberMeFlag
    ) {
    }

    public function isRememberMeCheckedForCustomer(RequestInterface $request): bool
    {
        return (bool) $request->getParam('remember_me');
    }

    public function isRememberMeCheckedForAdmin(RequestInterface $request): bool
    {
        // Prefer flag captured before Magento clears login POST in Authentication plugin.
        if ($this->adminRememberMeFlag->isChecked()) {
            return true;
        }

        $login = $request->getParam('login');

        if (!is_array($login)) {
            return false;
        }

        return !empty($login['remember_me']);
    }

    public function applyCustomerRememberMe(
        CustomerSession $session,
        ConfigInterface $sessionConfig,
        bool $rememberMe
    ): void {
        $this->applyRememberMe(
            $session,
            $sessionConfig,
            $rememberMe,
            $this->config->getCustomerLifetimeWithoutRemember(),
            $this->config->getCustomerLifetimeWithRemember()
        );
    }

    public function applyAdminRememberMe(
        AdminSession $session,
        ConfigInterface $sessionConfig,
        bool $rememberMe
    ): void {
        $this->applyRememberMe(
            $session,
            $sessionConfig,
            $rememberMe,
            $this->config->getAdminLifetimeWithoutRemember(),
            $this->config->getAdminLifetimeWithRemember()
        );
    }

    public function renewCustomerSessionCookie(CustomerSession $session, ConfigInterface $sessionConfig): void
    {
        if (!$session->isLoggedIn()) {
            return;
        }

        $rememberMe = (bool) $session->getData(self::SESSION_FLAG);
        $lifetime = $rememberMe
            ? $this->config->getCustomerLifetimeWithRemember()
            : $this->config->getCustomerLifetimeWithoutRemember();

        $sessionConfig->setCookieLifetime($lifetime);
        $this->renewSessionCookie($session, $sessionConfig, $lifetime);
    }

    public function renewAdminSessionCookie(AdminSession $session, ConfigInterface $sessionConfig): void
    {
        if (!$session->isLoggedIn()) {
            return;
        }

        $rememberMe = (bool) $session->getData(self::SESSION_FLAG);
        $lifetime = $rememberMe
            ? $this->config->getAdminLifetimeWithRemember()
            : $this->config->getAdminLifetimeWithoutRemember();

        $sessionConfig->setCookieLifetime($lifetime);
        $this->renewSessionCookie($session, $sessionConfig, $lifetime);
    }

    private function applyRememberMe(
        SessionManager $session,
        ConfigInterface $sessionConfig,
        bool $rememberMe,
        int $lifetimeWithout,
        int $lifetimeWith
    ): void {
        $session->setData(self::SESSION_FLAG, $rememberMe);
        $lifetime = $rememberMe ? $lifetimeWith : $lifetimeWithout;
        $sessionConfig->setCookieLifetime($lifetime);
        $this->renewSessionCookie($session, $sessionConfig, $lifetime);
    }

    private function renewSessionCookie(
        SessionManager $session,
        ConfigInterface $sessionConfig,
        int $lifetime
    ): void {
        if ($lifetime <= 0) {
            return;
        }

        $cookieValue = $session->getSessionId();
        if ($cookieValue === '') {
            return;
        }

        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($lifetime)
            ->setPath($sessionConfig->getCookiePath())
            ->setDomain($sessionConfig->getCookieDomain())
            ->setSecure($sessionConfig->getCookieSecure())
            ->setHttpOnly($sessionConfig->getCookieHttpOnly())
            ->setSameSite($sessionConfig->getCookieSameSite());

        $this->cookieManager->setPublicCookie($session->getName(), $cookieValue, $metadata);
    }
}
