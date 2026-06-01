<?php

declare(strict_types=1);

namespace Rokezzz\Compare\Plugin\Catalog\ViewModel\Product\Checker;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\ViewModel\Product\Checker\AddToCompareAvailability;
use Rokezzz\Compare\Model\Config;

class RestrictAddToCompareAvailabilityPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterIsAvailableForCompare(
        AddToCompareAvailability $subject,
        bool $result,
        ProductInterface $product
    ): bool {
        unset($subject, $product);

        return $result && $this->config->isEnabled();
    }
}
