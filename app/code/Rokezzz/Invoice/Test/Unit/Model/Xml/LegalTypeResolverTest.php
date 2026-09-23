<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model\Xml;

use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Invoice\LegalType;
use Rokezzz\Invoice\Model\Xml\LegalTypeResolver;

final class LegalTypeResolverTest extends TestCase
{
    public function testResolveReturnsStoredLegalType(): void
    {
        $invoice = $this->invoiceWithLegalType(LegalType::Correction->value);

        $this->assertSame(LegalType::Correction, (new LegalTypeResolver())->resolve($invoice));
    }

    public function testResolveFallsBackToStandardForUnknownValue(): void
    {
        $invoice = $this->invoiceWithLegalType('unknown');

        $this->assertSame(LegalType::Standard, (new LegalTypeResolver())->resolve($invoice));
    }

    private function invoiceWithLegalType(string $legalType): Invoice
    {
        $invoice = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData'])
            ->getMock();
        $invoice->method('getData')->willReturn($legalType);

        return $invoice;
    }
}
