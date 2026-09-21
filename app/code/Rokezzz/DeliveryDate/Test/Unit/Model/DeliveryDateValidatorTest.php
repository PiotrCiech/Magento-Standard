<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rokezzz\DeliveryDate\Model\Config;
use Rokezzz\DeliveryDate\Model\DeliveryDateValidator;

final class DeliveryDateValidatorTest extends TestCase
{
    private Config&MockObject $config;
    private DeliveryDateValidator $validator;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->validator = new DeliveryDateValidator($this->config);
    }

    public function testValidateReturnsNullForOptionalEmptyValue(): void
    {
        $this->config->method('isRequired')->willReturn(false);

        $this->assertNull($this->validator->validate(null));
        $this->assertNull($this->validator->validate(''));
    }

    public function testValidateThrowsWhenRequiredAndEmpty(): void
    {
        $this->config->method('isRequired')->willReturn(true);

        $this->expectException(LocalizedException::class);

        $this->validator->validate(null);
    }

    public function testValidateRejectsInvalidFormat(): void
    {
        $this->config->method('isRequired')->willReturn(false);

        $this->expectException(LocalizedException::class);

        $this->validator->validate('06-07-2026');
    }

    public function testValidateRejectsDateOutsideRange(): void
    {
        $this->config->method('isRequired')->willReturn(false);
        $this->config->method('getMinDate')->willReturn('2026-07-10');
        $this->config->method('getMaxDate')->willReturn('2026-07-20');

        $this->expectException(LocalizedException::class);

        $this->validator->validate('2026-07-05');
    }

    public function testValidateRejectsWeekendWhenConfigured(): void
    {
        $this->config->method('isRequired')->willReturn(false);
        $this->config->method('getMinDate')->willReturn('2026-07-01');
        $this->config->method('getMaxDate')->willReturn('2026-07-31');
        $this->config->method('isExcludeWeekends')->willReturn(true);

        $this->expectException(LocalizedException::class);

        $this->validator->validate('2026-07-05');
    }

    public function testValidateReturnsNormalizedDate(): void
    {
        $this->config->method('isRequired')->willReturn(false);
        $this->config->method('getMinDate')->willReturn('2026-07-01');
        $this->config->method('getMaxDate')->willReturn('2026-07-31');
        $this->config->method('isExcludeWeekends')->willReturn(false);

        $this->assertSame('2026-07-07', $this->validator->validate(' 2026-07-07 '));
    }
}
