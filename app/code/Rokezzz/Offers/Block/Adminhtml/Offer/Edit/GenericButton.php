<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;

abstract class GenericButton
{
    public function __construct(
        protected readonly Context $context
    ) {
    }

    protected function getOfferId(): int
    {
        return (int) $this->context->getRequest()->getParam('entity_id');
    }

    protected function getRequestId(): int
    {
        return (int) $this->context->getRequest()->getParam('request_id');
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function getUrl(string $route, array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    protected function getConfirmPostOnClick(string $message, string $url): string
    {
        $escapedMessage = $this->context->getEscaper()->escapeJs(
            $this->context->getEscaper()->escapeHtml($message)
        );

        return 'deleteConfirm(\'' . $escapedMessage . '\', \'' . $url . '\', {"data": {}})';
    }
}
