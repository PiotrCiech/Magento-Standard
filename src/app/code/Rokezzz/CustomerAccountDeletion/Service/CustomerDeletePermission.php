<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Service;

class CustomerDeletePermission
{
    private bool $allowed = false;

    public function allowNextCustomerDelete(): void
    {
        $this->allowed = true;
    }

    public function consume(): bool
    {
        if (!$this->allowed) {
            return false;
        }

        $this->allowed = false;

        return true;
    }
}
