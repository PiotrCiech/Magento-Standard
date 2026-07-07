<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Checkout;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Rokezzz\DeliveryDate\Model\Config;
use Rokezzz\DeliveryDate\Model\DeliveryDateRequestReader;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;

class GuestPaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly DeliveryDateRequestReader $deliveryDateRequestReader,
        private readonly DeliveryDateSaver $deliveryDateSaver
    ) {
    }

    /**
     * @param mixed $cartId
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagement $subject,
        $cartId,
        string $email,
        PaymentInterface $paymentMethod,
        ?QuoteAddressInterface $billingAddress = null
    ): void {
        if (!$this->config->isEnabled()) {
            return;
        }

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load((string) $cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();

        if ($quoteId === 0) {
            return;
        }

        $deliveryDate = $this->deliveryDateRequestReader->fromPayment($paymentMethod);
        $this->deliveryDateSaver->save($quoteId, $deliveryDate);
    }
}
