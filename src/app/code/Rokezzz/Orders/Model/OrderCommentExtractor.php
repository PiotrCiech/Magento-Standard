<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class OrderCommentExtractor
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

        $getter = 'getOrderComment';

        if (!method_exists($extensionAttributes, $getter)) {
            return null;
        }

        $comment = $extensionAttributes->{$getter}();

        return is_string($comment) ? $comment : null;
    }
}
