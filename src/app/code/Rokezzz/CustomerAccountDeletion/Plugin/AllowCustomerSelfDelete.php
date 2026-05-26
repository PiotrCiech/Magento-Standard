<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Plugin;

use Magento\Customer\Model\Customer;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ActionValidator\RemoveAction;
use Rokezzz\CustomerAccountDeletion\Service\CustomerDeletePermission;

class AllowCustomerSelfDelete
{
    public function __construct(
        private readonly CustomerDeletePermission $customerDeletePermission
    ) {
    }

    public function aroundIsAllowed(RemoveAction $subject, callable $proceed, AbstractModel $model): bool
    {
        unset($subject);

        if ($this->isCustomerModel($model) && $this->customerDeletePermission->consume()) {
            return true;
        }

        return $proceed($model);
    }

    private function isCustomerModel(AbstractModel $model): bool
    {
        return $model instanceof Customer;
    }
}
