<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Rokezzz\PromotionSlider\Model\ResourceModel\Slide\CollectionFactory;
use Magento\Store\Model\ScopeInterface;

class Slider extends Template
{
    protected $_template = 'Rokezzz_PromotionSlider::slider.phtml';

    private CollectionFactory $collectionFactory;
    private ?array $slides = null;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    protected function _toHtml()
    {
        if (!$this->_scopeConfig->isSetFlag('rokezzz_settings/general/enable', ScopeInterface::SCOPE_STORE)) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getSlides(): array
    {
        if ($this->slides !== null) {
            return $this->slides;
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setOrder('sort_order', 'ASC');

        $this->slides = [];
        foreach ($collection as $slide) {
            $this->slides[] = [
                'image' => $slide->getImage(),
                'title' => $slide->getTitle(),
                'btn_left_label' => $slide->getData('btn_left_label'),
                'btn_left_url' => $slide->getData('btn_left_url'),
                'btn_right_label' => $slide->getData('btn_right_label'),
                'btn_right_url' => $slide->getData('btn_right_url'),
                'counter_left_label' => $slide->getData('counter_left_label'),
                'counter_left_number' => $slide->getData('counter_left_number'),
                'counter_right_label' => $slide->getData('counter_right_label'),
                'counter_right_number' => $slide->getData('counter_right_number'),
            ];
        }

        return $this->slides;
    }

    public function getImageUrl(string $imagePath): string
    {
        if (!$imagePath) {
            return '';
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        return $this->_storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'rokezzz/promotionslider/' . ltrim($imagePath, '/');
    }

    public function getUniqueId(): string
    {
        return 'promotion-slider-' . uniqid();
    }

    public function isAutoplay(): bool
    {
        return true;
    }

    public function getAutoplaySpeed(): int
    {
        return 5000;
    }

    public function getAnimationSpeed(): int
    {
        return 500;
    }

    public function showArrows(): bool
    {
        return true;
    }

    public function showDots(): bool
    {
        return true;
    }

    public function hasSlides(): bool
    {
        return count($this->getSlides()) > 0;
    }
}
