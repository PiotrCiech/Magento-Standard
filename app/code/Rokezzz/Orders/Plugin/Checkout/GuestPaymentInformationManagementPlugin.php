<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Checkout;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentRequestReader;
use Rokezzz\Orders\Model\OrderCommentSaver;

final class GuestPaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly OrderCommentRequestReader $orderCommentRequestReader,
        private readonly OrderCommentSaver $orderCommentSaver
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

        $comment = $this->orderCommentRequestReader->fromPayment($paymentMethod);
        $this->orderCommentSaver->save($quoteId, $comment);
    }
}
