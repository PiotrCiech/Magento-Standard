<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Plugin\Checkout;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Rokezzz\OrderAdvance\Model\Config;
use Rokezzz\OrderAdvance\Model\OrderNumberRequestReader;
use Rokezzz\OrderAdvance\Model\OrderNumberSaver;

final class ShippingInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderNumberRequestReader $orderNumberRequestReader,
        private readonly OrderNumberSaver $orderNumberSaver
    ) {
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): void {
        $this->persistOrderNumber($cartId, $addressInformation);
    }

    public function afterSaveAddressInformation(
        ShippingInformationManagement $subject,
        PaymentDetailsInterface $result,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): PaymentDetailsInterface {
        $this->persistOrderNumber($cartId, $addressInformation);

        return $result;
    }

    private function persistOrderNumber(int $cartId, ShippingInformationInterface $addressInformation): void
    {
        if (!$this->config->isOrderNumberEnabled()) {
            return;
        }

        $orderNumber = $this->orderNumberRequestReader->fromShippingInformation($addressInformation);
        $this->orderNumberSaver->save($cartId, $orderNumber);
    }
}
