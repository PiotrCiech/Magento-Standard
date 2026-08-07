<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Model;

use Magento\Framework\App\RequestInterface;

final class AdminRememberMeFlag
{
    private bool $checked = false;

    public function captureFromRequest(RequestInterface $request): void
    {
        $this->checked = false;
        $login = $request->getParam('login');

        if (!is_array($login)) {
            return;
        }

        $this->checked = !empty($login['remember_me']);
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }
}
