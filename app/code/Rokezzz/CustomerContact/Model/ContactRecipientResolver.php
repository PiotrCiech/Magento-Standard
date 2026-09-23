<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model;

use Magento\Contact\Model\ConfigInterface;

final class ContactRecipientResolver
{
    public function __construct(
        private readonly ConfigInterface $contactsConfig,
        private readonly AssignedAdminProvider $assignedAdminProvider
    ) {
    }

    public function resolve(): string
    {
        $assignedAdmin = $this->assignedAdminProvider->get();

        if (!is_null($assignedAdmin)) {
            return $assignedAdmin->getEmail();
        }

        return $this->contactsConfig->emailRecipient();
    }
}
