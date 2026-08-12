<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Block\Adminhtml\Slide\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData(): array
    {
        $slideId = (int)$this->context->getRequest()->getParam('slide_id');
        if (!$slideId) {
            return [];
        }

        return [
            'label' => __('Delete Slide'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this slide?')
                . '\', \'' . $this->getDeleteUrl($slideId) . '\', {data: {}})',
            'sort_order' => 20,
        ];
    }

    private function getDeleteUrl(int $slideId): string
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['slide_id' => $slideId]);
    }
}
