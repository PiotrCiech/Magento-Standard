<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Test\Unit\Model;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\Gus\Model\GusApiExceptionMapper;

final class GusApiExceptionMapperTest extends TestCase
{
    private GusApiExceptionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new GusApiExceptionMapper();
    }

    public function testToLocalizedExceptionMapsInvalidUserKey(): void
    {
        $exception = $this->mapper->toLocalizedException(new InvalidUserKeyException());

        $this->assertInstanceOf(LocalizedException::class, $exception);
        $this->assertSame('Invalid GUS API key.', $exception->getMessage());
    }

    public function testToLocalizedExceptionMapsNotFound(): void
    {
        $exception = $this->mapper->toLocalizedException(new NotFoundException());

        $this->assertInstanceOf(LocalizedException::class, $exception);
        $this->assertSame('No company found for the given NIP.', $exception->getMessage());
    }
}
