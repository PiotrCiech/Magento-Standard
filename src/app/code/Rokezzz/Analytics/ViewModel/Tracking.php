<?php

declare(strict_types=1);

namespace Rokezzz\GoogleAnalytics\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\GoogleAnalytics\Model\Config;

class Tracking implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function isGtagActive(): bool
    {
        return $this->config->isGtagActive();
    }

    public function isGtmActive(): bool
    {
        return $this->config->isGtmActive();
    }

    public function getTrackingId(): string
    {
        return $this->config->getTrackingId();
    }
}
