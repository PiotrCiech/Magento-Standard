<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Block\Adminhtml\Author\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getButtonData(): array
    {
        $authorId = (int) $this->context->getRequest()->getParam('author_id');
        if ($authorId <= 0) {
            return [];
        }

        return [
            'label' => __('Delete Author'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this author?')
                . '\', \''
                . $this->context->getUrlBuilder()->getUrl('*/*/delete', ['author_id' => $authorId])
                . '\', {data: {}})',
            'sort_order' => 20,
        ];
    }
}
