<?php
declare(strict_types=1);

namespace Rokezzz\Acordeon\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;

class AccordionRows extends Template
{
    protected $_template = 'Rokezzz_Acordeon::widget/accordion_rows.phtml';
    protected $_element;

    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $this->_element = $element;
        $element->setData('after_element_html', $this->toHtml());

        return $element;
    }

    public function getElement(): AbstractElement
    {
        return $this->_element;
    }
}
