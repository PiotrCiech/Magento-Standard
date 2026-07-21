<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Service;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;

final class ValidateCustomerPassword
{
    public function __construct(
        private readonly AuthenticationInterface $authentication,
        private readonly CustomerRegistry $customerRegistry,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function execute(int $customerId, string $password): bool
    {
        $passwordHash = $this->customerRegistry
            ->retrieveSecureData($customerId)
            ->getPasswordHash() ?? '';

        if ($this->encryptor->validateHash($password, $passwordHash)) {
            return true;
        }

        $this->authentication->processAuthenticationFailure($customerId);

        return false;
    }
}
