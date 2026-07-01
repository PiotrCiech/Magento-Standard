<?php

declare(strict_types=1);

namespace Rokezzz\Orders\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Rokezzz\Orders\Model\Config;
use Rokezzz\Orders\Model\OrderCommentExtractor;
use Rokezzz\Orders\Model\OrderCommentSaver;

class ShippingInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderCommentExtractor $orderCommentExtractor,
        private readonly OrderCommentSaver $orderCommentSaver
    ) {
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): void {
        if (!$this->config->isEnabled()) {
            return;
        }

        $comment = $this->orderCommentExtractor->fromShippingInformation($addressInformation);
        $this->orderCommentSaver->save($cartId, $comment);
    }
}
