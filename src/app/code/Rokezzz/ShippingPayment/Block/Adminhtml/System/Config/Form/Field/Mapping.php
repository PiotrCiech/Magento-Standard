<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Rokezzz\ShippingPayment\Model\PaymentMethodOptions;
use Rokezzz\ShippingPayment\Model\ShippingMethodOptions;

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
        private readonly ShippingMethodOptions $shippingMethodOptions,
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
    public function getShippingOptions(): array
    {
        return $this->shippingMethodOptions->toOptionArray();
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

    public function getFieldId(string $shippingCode): string
    {
        return $this->formElement->getHtmlId() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $shippingCode);
    }

    public function getFieldName(string $shippingCode): string
    {
        return $this->formElement->getName() . '[' . $shippingCode . ']';
    }

    /**
     * @return string[]
     */
    public function getSelectedPaymentCodes(string $shippingCode): array
    {
        $value = $this->formElement->getValue();

        if (!is_array($value)) {
            return [];
        }

        $selected = $value[$shippingCode] ?? [];

        return is_array($selected) ? array_map('strval', $selected) : [];
    }
}
