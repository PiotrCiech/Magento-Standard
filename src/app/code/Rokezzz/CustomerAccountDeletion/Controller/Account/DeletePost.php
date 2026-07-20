<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Controller\Account;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Rokezzz\CustomerAccountDeletion\Model\DeleteCustomerAccountPostHandler;

final class DeletePost implements AccountInterface, HttpPostActionInterface
{
    public function __construct(
        private readonly DeleteCustomerAccountPostHandler $postHandler
    ) {
    }

    public function execute(): Redirect
    {
        return $this->postHandler->execute();
    }
}
