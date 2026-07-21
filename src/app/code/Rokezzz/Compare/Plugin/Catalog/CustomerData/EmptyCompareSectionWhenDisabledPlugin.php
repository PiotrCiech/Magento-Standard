<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Plugin\Catalog\CustomerData;

use Magento\Catalog\CustomerData\CompareProducts;
use Rokezzz\Compare\Model\Config;

final class EmptyCompareSectionWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterGetSectionData(CompareProducts $subject, array $result): array
    {
        unset($subject);

        if ($this->config->isEnabled()) {
            return $result;
        }

        return [
            'count' => 0,
            'countCaption' => '',
            'listUrl' => '',
            'items' => [],
            'storeId' => $result['storeId'] ?? null,
        ];
    }
}
