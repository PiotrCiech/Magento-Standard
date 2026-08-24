<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CategoryActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['category_id'])) {
                continue;
            }

            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl('rokezzz_blog/category/edit', ['category_id' => $item['category_id']]),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl('rokezzz_blog/category/delete', ['category_id' => $item['category_id']]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete Category'),
                        'message' => __('Are you sure you want to delete this category?'),
                    ],
                    'post' => true,
                ],
            ];
        }

        return $dataSource;
    }
}
