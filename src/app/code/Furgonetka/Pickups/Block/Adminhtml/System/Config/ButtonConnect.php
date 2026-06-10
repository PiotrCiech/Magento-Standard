<?php

namespace Furgonetka\Pickups\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ButtonConnect extends Field
{
    protected $_template = 'Furgonetka_Pickups::system/config/button.phtml';
    private $helperFunctions;

    public function __construct(
        Context $context,
        \Furgonetka\Pickups\Helper\HelperFunctions $helperFunctions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperFunctions = $helperFunctions;
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->isConnected() ? $this->getUrl('furgonetka_admin/integration/disconnect') : $this->getUrl('furgonetka_admin/integration/connect');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'furgonetka_connect_btn',
                'label' => $this->isConnected() ? __('Resetuj') : __('Połącz'),
            ]
        );
        return $button->toHtml();
    }

    private function isConnected()
    {
        return $this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::SOURCE_ID);
    }

    public function canShow()
    {
        return ($this->helperFunctions->getConfig(\Furgonetka\Pickups\Helper\Data::IS_TEST_MODE));
    }
}