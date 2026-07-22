<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Backend\App\Action;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Rokezzz\RememberMe\Model\AdminRememberMeFlag;

final class CaptureAdminRememberMePlugin
{
    public function __construct(
        private readonly AdminRememberMeFlag $adminRememberMeFlag
    ) {
    }

    /**
     * @return mixed
     */
    public function aroundDispatch(
        AbstractAction $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        $this->adminRememberMeFlag->captureFromRequest($request);

        return $proceed($request);
    }
}
