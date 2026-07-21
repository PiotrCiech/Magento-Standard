<?php

declare(strict_types=1);

namespace Rokezzz\AvailabilityNotification\Plugin\ProductAlert\Block\Product\View;

use Magento\ProductAlert\Block\Product\View\Stock;
use Rokezzz\AvailabilityNotification\Model\Config;

final class HideStockAlertBlockWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function aroundToHtml(Stock $subject, callable $proceed): string
    {
        unset($subject);

        if (!$this->config->isEnabled()) {
            return '';
        }

        return $proceed();
    }
}
