<?php

declare(strict_types=1);

namespace Rokezzz\CustomerContact\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\CustomerContact\Model\PhoneValidator;

final class PhoneValidatorTest extends TestCase
{
    private PhoneValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PhoneValidator();
    }

    public function testNormalizeOptionalAllowsEmptyValue(): void
    {
        $this->assertSame('', $this->validator->normalizeOptional(''));
        $this->assertSame('', $this->validator->normalizeOptional('   '));
    }

    public function testNormalizeOptionalAcceptsPolishMobileNumber(): void
    {
        $this->assertSame('512345678', $this->validator->normalizeOptional('512 345 678'));
    }

    public function testNormalizeOptionalAcceptsNumberWithCountryCode(): void
    {
        $this->assertSame('512345678', $this->validator->normalizeOptional('+48 512 345 678'));
    }

    public function testNormalizeOptionalRejectsInvalidValue(): void
    {
        $this->expectException(LocalizedException::class);

        $this->validator->normalizeOptional('abc-def-ghi');
    }
}
