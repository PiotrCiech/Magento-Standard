<?php

declare(strict_types=1);

namespace Rokezzz\DeliveryDate\Plugin\Checkout;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Rokezzz\DeliveryDate\Model\Config;
use Rokezzz\DeliveryDate\Model\DeliveryDateRequestReader;
use Rokezzz\DeliveryDate\Model\DeliveryDateSaver;

final class ShippingInformationManagementPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly DeliveryDateRequestReader $deliveryDateRequestReader,
        private readonly DeliveryDateSaver $deliveryDateSaver
    ) {
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): void {
        $this->persistDeliveryDate($cartId, $addressInformation);
    }

    public function afterSaveAddressInformation(
        ShippingInformationManagement $subject,
        PaymentDetailsInterface $result,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ): PaymentDetailsInterface {
        $this->persistDeliveryDate($cartId, $addressInformation);

        return $result;
    }

    private function persistDeliveryDate(int $cartId, ShippingInformationInterface $addressInformation): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $deliveryDate = $this->deliveryDateRequestReader->fromShippingInformation($addressInformation);
        $this->deliveryDateSaver->save($cartId, $deliveryDate);
    }
}
