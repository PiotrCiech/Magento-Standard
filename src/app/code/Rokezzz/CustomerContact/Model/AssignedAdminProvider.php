<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;

class AssignedAdminProvider
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly AdminUserProvider $adminUserProvider
    ) {
    }

    public function get(): ?AssignedAdmin
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        $customerId = (int) $this->customerSession->getCustomerId();
        if ($customerId <= 0) {
            return null;
        }

        $customer = $this->customerRepository->getById($customerId);
        $attribute = $customer->getCustomAttribute(AssignedAdminAttribute::CODE);

        if (is_null($attribute)) {
            return null;
        }

        $assignedAdminUserId = (int) $attribute->getValue();

        return $this->adminUserProvider->getById($assignedAdminUserId);
    }
}
