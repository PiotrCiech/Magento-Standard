<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\Settings\Model\NipValidator;

final class NipValidatorTest extends TestCase
{
    private NipValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new NipValidator();
    }

    public function testNormalizeOptionalAllowsEmptyValue(): void
    {
        $this->assertSame('', $this->validator->normalizeOptional(''));
        $this->assertSame('', $this->validator->normalizeOptional('   '));
    }

    public function testNormalizeOptionalAcceptsValidNipWithSeparators(): void
    {
        $this->assertSame('7740001454', $this->validator->normalizeOptional('774-000-14-54'));
    }

    public function testNormalizeOptionalRejectsInvalidCharacters(): void
    {
        $this->expectException(LocalizedException::class);

        $this->validator->normalizeOptional('abc-def-ghi');
    }

    public function testNormalizeOptionalRejectsInvalidChecksum(): void
    {
        $this->expectException(LocalizedException::class);

        $this->validator->normalizeOptional('1234567890');
    }
}
