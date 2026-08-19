<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getButtonData(): array
    {
        $categoryId = (int) $this->context->getRequest()->getParam('category_id');
        if ($categoryId <= 0) {
            return [];
        }

        return [
            'label' => __('Delete Category'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this category?')
                . '\', \''
                . $this->context->getUrlBuilder()->getUrl('*/*/delete', ['category_id' => $categoryId])
                . '\', {data: {}})',
            'sort_order' => 20,
        ];
    }
}
