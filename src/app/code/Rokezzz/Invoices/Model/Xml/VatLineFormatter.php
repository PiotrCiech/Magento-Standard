<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Rokezzz\Invoices\Model\Data\InvoiceLine;
use Rokezzz\Invoices\Model\Invoice\Unit;

class VatLineFormatter
{
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
            return ['amount' => '0.00', 'code' => 'zw'];
        }

        return match ((int) round($line->getVatRate())) {
            23 => ['amount' => $this->formatAmount($line->getTotalVat()), 'code' => '23'],
            8 => ['amount' => $this->formatAmount($line->getTotalVat()), 'code' => '8'],
            5 => ['amount' => $this->formatAmount($line->getTotalVat()), 'code' => '5'],
            4 => ['amount' => $this->formatAmount($line->getTotalVat()), 'code' => '4'],
            3 => ['amount' => $this->formatAmount($line->getTotalVat()), 'code' => '3'],
            0 => ['amount' => '0.00', 'code' => '0'],
            default => ['amount' => '0.00', 'code' => '0'],
        };
    }

    private function resolveUnitLabel(string $unit): string
    {
        return match ($unit) {
            Unit::Service->value => 'usł.',
            Unit::Item->value => 'szt.',
            default => $unit,
        };
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
