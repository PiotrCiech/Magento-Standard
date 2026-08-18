<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model;

use Magento\User\Model\ResourceModel\User\CollectionFactory;

final class AdminUserProvider
{
    public function __construct(
        private readonly CollectionFactory $userCollectionFactory
    ) {
    }

    public function getById(int $userId): ?AssignedAdmin
    {
        if ($userId <= 0) {
            return null;
        }

        $collection = $this->userCollectionFactory->create();
        $collection->addFieldToFilter('main_table.user_id', $userId);
        $collection->addFieldToFilter('main_table.is_active', 1);

        $user = $collection->getFirstItem();
        if (!$user->getId()) {
            return null;
        }

        $email = (string) $user->getEmail();
        if ($email === '') {
            return null;
        }

        return new AssignedAdmin(
            (int) $user->getId(),
            $this->resolveName($user),
            $email
        );
    }

    private function resolveName(object $user): string
    {
        $name = trim(sprintf(
            '%s %s',
            (string) $user->getFirstName(),
            (string) $user->getLastName()
        ));

        if ($name !== '') {
            return $name;
        }

        return (string) $user->getUserName();
    }
}
