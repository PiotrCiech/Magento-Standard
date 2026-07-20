<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\OrderAdvance\Model\Config;

class StatusBar implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->config->isStatusBarEnabled();
    }
}
