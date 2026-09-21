<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Block\Adminhtml\Tag\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getButtonData(): array
    {
        $tagId = (int) $this->context->getRequest()->getParam('tag_id');
        if ($tagId <= 0) {
            return [];
        }

        return [
            'label' => __('Delete Tag'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this tag?')
                . '\', \''
                . $this->context->getUrlBuilder()->getUrl('*/*/delete', ['tag_id' => $tagId])
                . '\', {data: {}})',
            'sort_order' => 20,
        ];
    }
}
