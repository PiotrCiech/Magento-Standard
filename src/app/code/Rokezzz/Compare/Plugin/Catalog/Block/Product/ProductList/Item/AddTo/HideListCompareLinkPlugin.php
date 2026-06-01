<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Plugin\Catalog\Block\Product\ProductList\Item\AddTo;

use Magento\Catalog\Block\Product\ProductList\Item\AddTo\Compare;
use Rokezzz\Compare\Model\Config;

class HideListCompareLinkPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function aroundToHtml(Compare $subject, callable $proceed): string
    {
        unset($subject);

        if (!$this->config->isEnabled()) {
            return '';
        }

        return $proceed();
    }
}
