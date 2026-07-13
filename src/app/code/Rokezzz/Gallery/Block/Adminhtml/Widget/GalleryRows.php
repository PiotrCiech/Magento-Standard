<?php
declare(strict_types=1);
namespace Rokezzz\Gallery\Block\Adminhtml\Widget;
use Magento\{
    Backend\Block\Template,
    Framework\Data\Form\Element\AbstractElement
};
class GalleryRows extends Template
{
    protected $_template = 'Rokezzz_Gallery::widget/gallery_rows.phtml';
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
    public function getMediaBrowserUrl(string $targetId): string
    {
        return $this->getUrl(
            'cms/wysiwyg_images/index',
            ['target_element_id' => $targetId, 'type' => 'file']
        );
    }
}
