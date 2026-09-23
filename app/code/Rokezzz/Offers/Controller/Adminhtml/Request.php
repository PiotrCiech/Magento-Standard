<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Request extends Action
{
    public const ADMIN_RESOURCE = 'Rokezzz_Offers::request';
}
