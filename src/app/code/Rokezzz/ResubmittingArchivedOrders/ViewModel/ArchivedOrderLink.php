<?php

declare(strict_types=1);

namespace Rokezzz\ResubmittingArchivedOrders\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rokezzz\ResubmittingArchivedOrders\Model\Config;

class ArchivedOrderLink implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly UrlInterface $url
    ) {
    }

    public function isVisible(): bool
    {
        return $this->config->isEnabled();
    }

    public function getArchivedOrdersUrl(): string
    {
        return $this->url->getUrl('rokezzz_archived_order/order/history');
    }
}
