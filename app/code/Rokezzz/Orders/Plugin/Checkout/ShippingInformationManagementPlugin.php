<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Checkout;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentRequestReader;
use Rokezzz\Orders\Model\OrderCommentSaver;

final class ShippingInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderCommentRequestReader $orderCommentRequestReader,
        private readonly OrderCommentSaver $orderCommentSaver
    ) {
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): void {
        $this->persistComment($cartId, $addressInformation);
    }

    public function afterSaveAddressInformation(
        ShippingInformationManagement $subject,
        PaymentDetailsInterface $result,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): PaymentDetailsInterface {
        $this->persistComment($cartId, $addressInformation);

        return $result;
    }

    private function persistComment(int $cartId, ShippingInformationInterface $addressInformation): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $comment = $this->orderCommentRequestReader->fromShippingInformation($addressInformation);
        $this->orderCommentSaver->save($cartId, $comment);
    }
}
