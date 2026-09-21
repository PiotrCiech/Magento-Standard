<?php

declare(strict_types=1);

namespace Rokezzz\ChatBot\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\ChatBot\Model\Config;

class Widget implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function isActive(): bool
    {
        return $this->config->isActive();
    }

    public function getWidgetSrc(): string
    {
        return sprintf(
            'https://embed.tawk.to/%s/%s',
            $this->config->getPropertyId(),
            $this->config->getWidgetId()
        );
    }

    public function isAutoStart(): bool
    {
        return $this->config->isAutoStart();
    }

    public function isHiddenOnLoad(): bool
    {
        return $this->config->isHiddenOnLoad();
    }

    public function getZIndex(): ?int
    {
        return $this->config->getZIndex();
    }
}
