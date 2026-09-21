<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;

final class DeleteCustomerAccount
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Session $customerSession,
        private readonly CustomerDeletePermission $customerDeletePermission
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function execute(int $customerId): void
    {
        $this->customerSession->logout();
        $this->customerDeletePermission->allowNextCustomerDelete();
        $this->customerRepository->deleteById($customerId);
    }
}
