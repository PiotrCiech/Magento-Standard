<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Test\Unit\Model\Quote;

use PHPUnit\Framework\TestCase;
use Rokezzz\Offers\Model\Quote\QuoteRequestOperationContext;

final class QuoteRequestOperationContextTest extends TestCase
{
    public function testContextTracksNestedOperations(): void
    {
        $context = new QuoteRequestOperationContext();

        $context->begin();
        $context->begin();
        $context->end();

        $this->assertTrue($context->isActive());

        $context->end();

        $this->assertFalse($context->isActive());
    }

    public function testEndingInactiveContextKeepsItInactive(): void
    {
        $context = new QuoteRequestOperationContext();

        $context->end();

        $this->assertFalse($context->isActive());
    }
}
