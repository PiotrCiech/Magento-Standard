<?php

declare(strict_types=1);

namespace Rokezzz\RememberMe\Plugin\Backend\App\Action;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Rokezzz\RememberMe\Model\AdminRememberMeFlag;

/**
 * Magento clears login POST inside Authentication plugin before login success event.
 * Capture Remember Me in an outer around plugin, before Authentication runs.
 */
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
