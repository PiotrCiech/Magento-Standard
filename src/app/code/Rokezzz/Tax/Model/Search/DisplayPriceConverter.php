<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config as TaxConfig;

final class DisplayPriceConverter
{
    private const DEFAULT_PRODUCT_TAX_CLASS_PATH = 'tax/classes/default_product_tax_class';

    public function __construct(
        private readonly TaxConfig $taxConfig,
        private readonly TaxCalculationInterface $taxCalculation,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isConversionNeeded(int $storeId): bool
    {
        return (bool) $this->taxConfig->needPriceConversion($storeId);
    }

    public function getDefaultTaxClassId(int $storeId): int
    {
        return (int) $this->scopeConfig->getValue(
            self::DEFAULT_PRODUCT_TAX_CLASS_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function convert(float $price, int $taxClassId, int $storeId): float
    {
        if ($price <= 0 || !$this->isConversionNeeded($storeId)) {
            return $price;
        }

        $taxRate = (float) $this->taxCalculation->getCalculatedRate($taxClassId, null, $storeId);
        if ($taxRate <= 0) {
            return $price;
        }

        $catalogIncludesTax = $this->taxConfig->priceIncludesTax($storeId);
        $displayIncludesTax = $this->taxConfig->getPriceDisplayType($storeId) === TaxConfig::DISPLAY_TYPE_INCLUDING_TAX;
        $factor = 1 + ($taxRate / 100);

        if ($catalogIncludesTax && !$displayIncludesTax) {
            return round($price / $factor, 2);
        }

        if (!$catalogIncludesTax && $displayIncludesTax) {
            return round($price * $factor, 2);
        }

        return $price;
    }
}
