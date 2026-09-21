<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Rokezzz\Blog\Api\Data\CommentInterface;

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
            $item[$fieldName] = $this->renderStatus((string) ($item[$fieldName] ?? ''));
        }

        return $dataSource;
    }

    private function renderStatus(string $status): string
    {
        [$cssClass, $label] = match ($status) {
            CommentInterface::STATUS_APPROVED => ['grid-severity-notice', __('Approved')],
            CommentInterface::STATUS_REJECTED => ['grid-severity-major', __('Rejected')],
            default => ['grid-severity-minor', __('Pending')],
        };

        return sprintf(
            '<span class="%s"><span>%s</span></span>',
            $this->escaper->escapeHtmlAttr($cssClass),
            $this->escaper->escapeHtml((string) $label)
        );
    }
}
