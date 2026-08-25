<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Rokezzz\Blog\Model\Comment;

class CommentStatus extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly Escaper $escaper,
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

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] = $this->renderStatus((int)($item[$fieldName] ?? 0));
        }

        return $dataSource;
    }

    private function renderStatus(int $statusId): string
    {
        [$cssClass, $label] = match ($statusId) {
            Comment::STATUS_APPROVED => ['grid-severity-notice', __('Approved')],
            Comment::STATUS_NOT_APPROVED => ['grid-severity-major', __('Not Approved')],
            default => ['grid-severity-minor', __('Pending')],
        };

        return sprintf(
            '<span class="%s"><span>%s</span></span>',
            $this->escaper->escapeHtmlAttr($cssClass),
            $this->escaper->escapeHtml((string)$label)
        );
    }
}
