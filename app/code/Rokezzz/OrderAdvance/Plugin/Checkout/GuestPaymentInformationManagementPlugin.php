<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Plugin\Checkout;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\OrderNumberRequestReader;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

final class GuestPaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly OrderNumberRequestReader $orderNumberRequestReader,
        private readonly OrderNumberSaver $orderNumberSaver
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
        if (!$this->config->isOrderNumberEnabled()) {
            return;
        }

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load((string) $cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();

        if ($quoteId === 0) {
            return;
        }

        $orderNumber = $this->orderNumberRequestReader->fromPayment($paymentMethod);
        $this->orderNumberSaver->save($quoteId, $orderNumber);
    }
}
