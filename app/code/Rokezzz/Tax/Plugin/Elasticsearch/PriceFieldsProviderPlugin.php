<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Plugin\Elasticsearch;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\PriceFieldsProvider;
use Rokezzz\Tax\Model\Search\DisplayPriceConverter;
use Rokezzz\Tax\Model\Search\ProductTaxClassIdResolver;

final class PriceFieldsProviderPlugin
{
    public function __construct(
        private readonly DisplayPriceConverter $displayPriceConverter,
        private readonly ProductTaxClassIdResolver $productTaxClassIdResolver
    ) {
    }

    /**
     * @param array<int|string, mixed> $result
     * @param array<int|string> $productIds
     * @return array<int|string, mixed>
     */
    public function afterGetFields(
        PriceFieldsProvider $subject,
        array $result,
        array $productIds,
        $storeId
    ): array {
        $storeId = (int) $storeId;
        if ($result === [] || !$this->displayPriceConverter->isConversionNeeded($storeId)) {
            return $result;
        }

        $taxClassIds = $this->productTaxClassIdResolver->resolve(
            array_map('intval', $productIds),
            $storeId
        );
        $defaultTaxClassId = $this->displayPriceConverter->getDefaultTaxClassId($storeId);

        foreach ($result as $productId => $fields) {
            if (!is_array($fields)) {
                continue;
            }

            $taxClassId = $taxClassIds[(int) $productId] ?? $defaultTaxClassId;

            foreach ($fields as $fieldName => $price) {
                $result[$productId][$fieldName] = sprintf(
                    '%F',
                    $this->displayPriceConverter->convert((float) $price, $taxClassId, $storeId)
                );
            }
        }

        return $result;
    }
}
