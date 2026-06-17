<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\Gus\Model\NipNormalizer;

final class NipNormalizerTest extends TestCase
{
    private NipNormalizer $nipNormalizer;

    protected function setUp(): void
    {
        $this->nipNormalizer = new NipNormalizer();
    }

    public function testNormalizeStripsNonDigits(): void
    {
        $this->assertSame('1234567890', $this->nipNormalizer->normalize('123-456-78-90'));
    }

    public function testNormalizeAcceptsTenDigits(): void
    {
        $this->assertSame('1234567890', $this->nipNormalizer->normalize('1234567890'));
    }

    public function testNormalizeThrowsForInvalidLength(): void
    {
        $this->expectException(LocalizedException::class);

        $this->nipNormalizer->normalize('123');
    }
}
