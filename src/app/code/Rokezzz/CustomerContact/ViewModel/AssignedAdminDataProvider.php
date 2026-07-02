<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\CustomerContact\Model\AssignedAdminProvider;

class AssignedAdminDataProvider implements ArgumentInterface
{
    public function __construct(
        private readonly AssignedAdminProvider $assignedAdminProvider
    ) {
    }

    public function hasAssignedAdmin(): bool
    {
        return !is_null($this->assignedAdminProvider->get());
    }

    public function getName(): string
    {
        return $this->assignedAdminProvider->get()?->getName() ?? '';
    }

    public function getEmail(): string
    {
        return $this->assignedAdminProvider->get()?->getEmail() ?? '';
    }
}
