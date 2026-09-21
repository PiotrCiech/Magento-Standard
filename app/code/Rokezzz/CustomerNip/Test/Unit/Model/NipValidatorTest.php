<?php

declare(strict_types=1);

namespace Rokezzz\CustomerNip\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Rokezzz\CustomerNip\Model\NipValidator;

final class NipValidatorTest extends TestCase
{
    private NipValidator $nipValidator;

    protected function setUp(): void
    {
        $this->nipValidator = new NipValidator();
    }

    public function testIsValidReturnsTrueForValidNip(): void
    {
        $this->assertTrue($this->nipValidator->isValid('526-104-08-28'));
    }

    public function testIsValidReturnsFalseForInvalidChecksum(): void
    {
        $this->assertFalse($this->nipValidator->isValid('5261040820'));
    }

    public function testIsValidReturnsFalseForInvalidLength(): void
    {
        $this->assertFalse($this->nipValidator->isValid('123'));
    }
}
