<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Block\Adminhtml\Slide\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    private \Magento\Backend\Block\Widget\Context $context;

    public function __construct(\Magento\Backend\Block\Widget\Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    private function getBackUrl(): string
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/');
    }
}
