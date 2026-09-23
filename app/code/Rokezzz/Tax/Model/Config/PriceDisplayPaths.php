<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Config;

use Magento\Tax\Model\Config as TaxConfig;

final class PriceDisplayPaths
{
    /**
     * Magento tax display paths synchronized by the Rokezzz display mode toggle.
     *
     * @return string[]
     */
    public function getPaths(): array
    {
        return [
            TaxConfig::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
            TaxConfig::CONFIG_XML_PATH_DISPLAY_SHIPPING,
            TaxConfig::XML_PATH_DISPLAY_CART_PRICE,
            TaxConfig::XML_PATH_DISPLAY_CART_SUBTOTAL,
            TaxConfig::XML_PATH_DISPLAY_CART_SHIPPING,
            TaxConfig::XML_PATH_DISPLAY_SALES_PRICE,
            TaxConfig::XML_PATH_DISPLAY_SALES_SUBTOTAL,
            TaxConfig::XML_PATH_DISPLAY_SALES_SHIPPING,
        ];
    }
}
