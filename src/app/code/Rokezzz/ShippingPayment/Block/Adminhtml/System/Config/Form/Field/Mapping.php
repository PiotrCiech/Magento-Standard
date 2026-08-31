<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Rokezzz\ShippingPayment\Model\CarrierOptions;
use Rokezzz\ShippingPayment\Model\PaymentMethodOptions;

class Mapping extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Rokezzz_ShippingPayment::system/config/mapping.phtml';

    private ?AbstractElement $formElement = null;

    /**
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        private readonly CarrierOptions $carrierOptions,
        private readonly PaymentMethodOptions $paymentMethodOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->formElement = $element;

        return $this->_toHtml();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function getCarrierOptions(): array
    {
        return $this->carrierOptions->toOptionArray();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function getPaymentOptions(): array
    {
        return $this->paymentMethodOptions->toOptionArray();
    }

    public function getEmptyInputName(): string
    {
        return (string) $this->formElement->getName() . '[__empty]';
    }

    public function getFieldId(string $carrierCode): string
    {
        return $this->formElement->getHtmlId() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $carrierCode);
    }

    public function getFieldName(string $carrierCode): string
    {
        return $this->formElement->getName() . '[' . $carrierCode . ']';
    }

    /**
     * @return string[]
     */
    public function getSelectedPaymentCodes(string $carrierCode): array
    {
        $value = $this->formElement->getValue();

        if (!is_array($value)) {
            return [];
        }

        $selected = $value[$carrierCode] ?? [];

        return is_array($selected) ? array_map('strval', $selected) : [];
    }
}
