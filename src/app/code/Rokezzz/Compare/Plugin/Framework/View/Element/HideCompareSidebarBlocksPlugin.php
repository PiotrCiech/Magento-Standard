<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Plugin\Framework\View\Element;

use Magento\Framework\View\Element\Template;
use Rokezzz\Compare\Model\Config;

final class HideCompareSidebarBlocksPlugin
{
    private const BLOCK_NAMES = [
        'catalog.compare.sidebar',
        'catalog.compare.link',
    ];

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function aroundToHtml(Template $subject, callable $proceed): string
    {
        if (
            !$this->config->isEnabled()
            && in_array($subject->getNameInLayout(), self::BLOCK_NAMES, true)
        ) {
            return '';
        }

        return $proceed();
    }
}
