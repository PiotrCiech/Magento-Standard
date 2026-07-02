<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Test\Unit\Model;

use Magento\Contact\Model\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\CustomerContact\Model\AssignedAdmin;
use Rokezzz\CustomerContact\Model\AssignedAdminProvider;
use Rokezzz\CustomerContact\Model\ContactRecipientResolver;

final class ContactRecipientResolverTest extends TestCase
{
    private ConfigInterface&MockObject $contactsConfig;

    private AssignedAdminProvider&MockObject $assignedAdminProvider;

    protected function setUp(): void
    {
        $this->contactsConfig = $this->createMock(ConfigInterface::class);
        $this->assignedAdminProvider = $this->createMock(AssignedAdminProvider::class);
    }

    public function testResolveReturnsAssignedAdminEmail(): void
    {
        $this->assignedAdminProvider->method('get')->willReturn(
            new AssignedAdmin(7, 'Jan Kowalski', 'jan.kowalski@example.com')
        );
        $this->contactsConfig->expects($this->never())->method('emailRecipient');

        $resolver = new ContactRecipientResolver($this->contactsConfig, $this->assignedAdminProvider);

        $this->assertSame('jan.kowalski@example.com', $resolver->resolve());
    }

    public function testResolveFallsBackToDefaultRecipient(): void
    {
        $this->assignedAdminProvider->method('get')->willReturn(null);
        $this->contactsConfig->method('emailRecipient')->willReturn('contact@example.com');

        $resolver = new ContactRecipientResolver($this->contactsConfig, $this->assignedAdminProvider);

        $this->assertSame('contact@example.com', $resolver->resolve());
    }
}
