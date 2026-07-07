<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Checkout;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Rokezzz\DeliveryDate\Model\Config;
use Rokezzz\DeliveryDate\Model\DeliveryDateRequestReader;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;

class PaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly DeliveryDateRequestReader $deliveryDateRequestReader,
        private readonly DeliveryDateSaver $deliveryDateSaver
    ) {
    }

    /**
     * @param mixed $cartId
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        ?QuoteAddressInterface $billingAddress = null
    ): void {
        $this->persistDeliveryDate((int) $cartId, $paymentMethod);
    }

    private function persistDeliveryDate(int $cartId, PaymentInterface $paymentMethod): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $deliveryDate = $this->deliveryDateRequestReader->fromPayment($paymentMethod);
        $this->deliveryDateSaver->save($cartId, $deliveryDate);
    }
}
