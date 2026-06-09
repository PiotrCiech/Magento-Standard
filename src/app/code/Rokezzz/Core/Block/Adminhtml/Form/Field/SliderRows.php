<?php
namespace Rokezzz\Core\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class SliderRows extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('title', [
            'label' => __('Tytuł (H1)'),
            'class' => 'required-entry',
            'style' => 'width: 150px;'
        ]);

        $this->addColumn('description', [
            'label' => __('Opis'),
            'style' => 'width: 200px;'
        ]);

        $this->addColumn('button_text', [
            'label' => __('Tekst Przycisku'),
            'style' => 'width: 120px;'
        ]);

        $this->addColumn('button_url', [
            'label' => __('Link Przycisku'),
            'style' => 'width: 150px;'
        ]);

        $this->addColumn('image_url', [
            'label' => __('URL Zdjęcia'),
            'class' => 'required-entry',
            'style' => 'width: 200px;'
        ]);

        $this->addColumn('sort_order', [
            'label' => __('Kolejność'),
            'class' => 'validate-number',
            'style' => 'width: 80px;'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Dodaj nowy slajd');
    }
}