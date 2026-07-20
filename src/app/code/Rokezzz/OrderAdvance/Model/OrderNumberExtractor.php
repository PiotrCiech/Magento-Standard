<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\Data\PaymentInterface;

final class OrderNumberExtractor
{
    public function fromPayment(PaymentInterface $payment): ?string
    {
        return $this->extract($payment->getExtensionAttributes());
    }

    public function fromShippingInformation(ShippingInformationInterface $addressInformation): ?string
    {
        return $this->extract($addressInformation->getExtensionAttributes());
    }

    private function extract(?object $extensionAttributes): ?string
    {
        if (is_null($extensionAttributes)) {
            return null;
        }

        $getter = 'getCustomerOrderNumber';

        if (!method_exists($extensionAttributes, $getter)) {
            return null;
        }

        $orderNumber = $extensionAttributes->{$getter}();

        return is_string($orderNumber) ? $orderNumber : null;
    }
}
