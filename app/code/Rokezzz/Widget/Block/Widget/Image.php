<?php
declare(strict_types=1);

namespace Rokezzz\Widget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Cms\Model\Template\FilterProvider;

class Image extends Template implements BlockInterface
{
    protected $_template = 'Rokezzz_Widget::widget/image.phtml';
    protected $filterProvider;

    public function __construct(
        Template\Context $context,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    public function getImageUrl(): string
    {
        $image = (string) $this->getData('image');
        if (!$image) {
            return '';
        }

        try {
            return $this->filterProvider->getBlockFilter()->filter($image);
        } catch (\Exception $e) {
            return $image;
        }
    }
}
