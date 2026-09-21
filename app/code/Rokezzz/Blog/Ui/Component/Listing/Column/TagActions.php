<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class TagActions extends Column
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
            if (!isset($item['tag_id'])) {
                continue;
            }

            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl('rokezzz_blog/tag/edit', ['tag_id' => $item['tag_id']]),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl('rokezzz_blog/tag/delete', ['tag_id' => $item['tag_id']]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete Tag'),
                        'message' => __('Are you sure you want to delete this tag?'),
                    ],
                    'post' => true,
                ],
            ];
        }

        return $dataSource;
    }
}
