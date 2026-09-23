<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Test\Unit\Model\Xml;

use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Rokezzz\Invoice\Model\Data\InvoiceLine;
use Rokezzz\Invoice\Model\Invoice\Unit;
use Rokezzz\Invoice\Model\Xml\VatLineFormatter;

final class VatLineFormatterTest extends TestCase
{
    private VatLineFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new VatLineFormatter();
    }

    public function testFormatMapsStandardVatRateAndUnitLabel(): void
    {
        $line = new InvoiceLine('Widget', Unit::Item->value, 2.5, 100.0, 23.0, 23.0, false);

        $this->assertSame(
            [
                'name' => 'Widget',
                'unitLabel' => 'szt.',
                'quantity' => '2.5',
                'netAmount' => '100.00',
                'vatAmount' => '23.00',
                'vatCode' => '23',
            ],
            $this->formatter->format($line)
        );
    }

    public function testFormatUsesExemptCodeAndZeroVatAmount(): void
    {
        $line = new InvoiceLine('Service', Unit::Service->value, 1.0, 50.0, 0.0, 23.0, true);

        $formatted = $this->formatter->format($line);

        $this->assertSame('usł.', $formatted['unitLabel']);
        $this->assertSame('0.00', $formatted['vatAmount']);
        $this->assertSame('zw', $formatted['vatCode']);
    }

    public function testFormatThrowsForUnsupportedVatRate(): void
    {
        $this->expectException(LocalizedException::class);

        $this->formatter->format(
            new InvoiceLine('Widget', Unit::Item->value, 1.0, 10.0, 1.9, 19.0, false)
        );
    }
}
