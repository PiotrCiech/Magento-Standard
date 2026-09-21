<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Xml;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Rokezzz\Invoice\Model\Invoice\LegalType;
use Rokezzz\Invoice\Model\Invoice\PaymentMethod;

final class PaymentMethodResolver
{
    public function resolve(InvoiceInterface $invoice): PaymentMethod
    {
        $order = $invoice->getOrder();
        if (!$order instanceof OrderInterface) {
            return PaymentMethod::BankTransfer;
        }

        $paymentMethod = (string) $order->getPayment()?->getMethod();

        if (in_array($paymentMethod, ['cashondelivery', 'cash'], true)) {
            return PaymentMethod::Cash;
        }

        if (in_array($paymentMethod, ['free', 'prepaid', 'checkmo'], true)) {
            return PaymentMethod::PaidInAdvance;
        }

        return PaymentMethod::BankTransfer;
    }
}
