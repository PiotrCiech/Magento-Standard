<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Invoices\Model\Data\InvoiceLine;
use Rokezzz\Invoices\Model\Invoice\Unit;

final class VatLineFormatter
{
    private const VAT_EXEMPT_CODE = 'zw';
    private const ZERO_VAT_CODE = '0';

    /** @var array<int, string> */
    private const KSEF_VAT_CODES = [
        23 => '23',
        8 => '8',
        5 => '5',
        4 => '4',
        3 => '3',
        0 => self::ZERO_VAT_CODE,
    ];

    /** @var array<string, string> */
    private const UNIT_LABELS = [
        Unit::Service->value => 'usł.',
        Unit::Item->value => 'szt.',
    ];

    /**
     * @return array{name: string, unitLabel: string, quantity: string, netAmount: string, vatAmount: string, vatCode: string}
     */
    public function format(InvoiceLine $line): array
    {
        $vatAmounts = $this->resolveVatAmounts($line);

        return [
            'name' => $line->getName(),
            'unitLabel' => $this->resolveUnitLabel($line->getUnit()),
            'quantity' => $this->formatQuantity($line->getQuantity()),
            'netAmount' => $this->formatAmount($line->getTotalNet()),
            'vatAmount' => $vatAmounts['amount'],
            'vatCode' => $vatAmounts['code'],
        ];
    }

    /**
     * @return array{amount: string, code: string}
     */
    private function resolveVatAmounts(InvoiceLine $line): array
    {
        if ($line->isVatExempt()) {
            return ['amount' => '0.00', 'code' => self::VAT_EXEMPT_CODE];
        }

        $vatRate = (int) round($line->getVatRate());
        $vatCode = self::KSEF_VAT_CODES[$vatRate] ?? null;

        if ($vatCode === null) {
            throw new LocalizedException(
                __('Unsupported VAT rate "%1" for KSeF invoice line.', $vatRate)
            );
        }

        return [
            'amount' => $vatRate === 0 ? '0.00' : $this->formatAmount($line->getTotalVat()),
            'code' => $vatCode,
        ];
    }

    private function resolveUnitLabel(string $unit): string
    {
        return self::UNIT_LABELS[$unit] ?? $unit;
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function formatQuantity(float $quantity): string
    {
        $formatted = number_format($quantity, 4, '.', '');

        return rtrim(rtrim($formatted, '0'), '.');
    }
}
