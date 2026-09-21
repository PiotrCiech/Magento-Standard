<?php
declare(strict_types=1);

namespace Rokezzz\Widget\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class ImageChooser extends Template
{
    protected $elementFactory;
    protected $wysiwygConfig;

    public function __construct(
        Template\Context $context,
        ElementFactory $elementFactory,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $wysiwygConfig = $this->wysiwygConfig->getConfig();
        if ($wysiwygConfig->hasData('plugins')) {
            $element->setWysiwygConfig($wysiwygConfig);
        }

        $sourceUrl = $this->getUrl(
            'cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']
        );

        $chooser = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel(__('Wybierz zdjęcie'))
            ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\')')
            ->setDisabled($element->getReadonly());

        $input = $this->elementFactory->create('text', ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass('widget-option input-text admin__control-text');

        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $jsScript = "
        <script>
            require(['jquery', 'mage/adminhtml/browser'], function($) {
            });
        </script>";

        $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml() . $jsScript);

        return $element;
    }
}
