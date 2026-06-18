<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Data;

class InvoiceLine
{
    public function __construct(
        private readonly string $name,
        private readonly string $unit,
        private readonly float $quantity,
        private readonly float $totalNet,
        private readonly float $totalVat,
        private readonly float $vatRate,
        private readonly bool $isVatExempt
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getTotalNet(): float
    {
        return $this->totalNet;
    }

    public function getTotalVat(): float
    {
        return $this->totalVat;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }

    public function isVatExempt(): bool
    {
        return $this->isVatExempt;
    }
}
