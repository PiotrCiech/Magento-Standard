<?php

declare(strict_types=1);

namespace Rokezzz\CustomerAccountDeletion\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Rokezzz\CustomerAccountDeletion\Service\CustomerDeletePermission;

final class CustomerDeletePermissionTest extends TestCase
{
    public function testConsumeReturnsFalseUntilAllowIsCalled(): void
    {
        $permission = new CustomerDeletePermission();

        $this->assertFalse($permission->consume());
    }

    public function testConsumeAllowsOnlyTheNextDelete(): void
    {
        $permission = new CustomerDeletePermission();
        $permission->allowNextCustomerDelete();

        $this->assertTrue($permission->consume());
        $this->assertFalse($permission->consume());
    }
}
