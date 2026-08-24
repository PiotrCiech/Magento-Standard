<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CommentActions extends Column
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
            if (!isset($item['comment_id'])) {
                continue;
            }

            $item[$this->getData('name')] = [
                'approve' => [
                    'href' => $this->urlBuilder->getUrl(
                        'rokezzz_blog/comment/approve',
                        ['comment_id' => $item['comment_id']]
                    ),
                    'label' => __('Accept'),
                    'post' => true,
                ],
                'reject' => [
                    'href' => $this->urlBuilder->getUrl(
                        'rokezzz_blog/comment/reject',
                        ['comment_id' => $item['comment_id']]
                    ),
                    'label' => __('Reject'),
                    'post' => true,
                ],
            ];
        }

        return $dataSource;
    }
}
