<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Rokezzz\Invoices\Model\Data\InvoiceLine;
use Rokezzz\Invoices\Model\Invoice\Unit;

class InvoiceLineBuilder
{
    /**
     * @return InvoiceLine[]
     */
    public function build(InvoiceInterface $invoice): array
    {
        $lines = [];

        foreach ($invoice->getItems() as $item) {
            if (!$item instanceof InvoiceItemInterface || (float) $item->getQty() <= 0) {
                continue;
            }

            $lines[] = $this->buildLine($item);
        }

        return $lines;
    }

    private function buildLine(InvoiceItemInterface $item): InvoiceLine
    {
        $vatRate = 0.0;
        if ($item instanceof InvoiceItem) {
            $vatRate = (float) ($item->getOrderItem()?->getTaxPercent() ?? 0);
        }
        $totalNet = (float) $item->getRowTotal();
        $totalVat = (float) $item->getTaxAmount();
        $isVatExempt = $totalVat <= 0.0 && $vatRate <= 0.0;

        return new InvoiceLine(
            name: (string) $item->getName(),
            unit: Unit::Item->value,
            quantity: (float) $item->getQty(),
            totalNet: $totalNet,
            totalVat: $totalVat,
            vatRate: $vatRate,
            isVatExempt: $isVatExempt
        );
    }
}
