<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Plugin\Checkout;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\OrderNumberRequestReader;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

final class PaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderNumberRequestReader $orderNumberRequestReader,
        private readonly OrderNumberSaver $orderNumberSaver
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
        $this->persistOrderNumber((int) $cartId, $paymentMethod);
    }

    private function persistOrderNumber(int $cartId, PaymentInterface $paymentMethod): void
    {
        if (!$this->config->isOrderNumberEnabled()) {
            return;
        }

        $orderNumber = $this->orderNumberRequestReader->fromPayment($paymentMethod);
        $this->orderNumberSaver->save($cartId, $orderNumber);
    }
}
