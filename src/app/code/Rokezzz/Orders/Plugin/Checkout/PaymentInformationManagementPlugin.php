<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Checkout;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentExtractor;
use Rokezzz\Orders\Model\OrderCommentSaver;

class PaymentInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderCommentExtractor $orderCommentExtractor,
        private readonly OrderCommentSaver $orderCommentSaver
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
        $this->saveOrderComment((int) $cartId, $paymentMethod);
    }

    private function saveOrderComment(int $cartId, PaymentInterface $paymentMethod): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $comment = $this->orderCommentExtractor->fromPayment($paymentMethod);
        $this->orderCommentSaver->save($cartId, $comment);
    }
}
