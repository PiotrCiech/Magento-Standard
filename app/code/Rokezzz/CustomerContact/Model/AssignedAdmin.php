<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Model;

final readonly class AssignedAdmin
{
    public function __construct(
        private int $userId,
        private string $name,
        private string $email
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
