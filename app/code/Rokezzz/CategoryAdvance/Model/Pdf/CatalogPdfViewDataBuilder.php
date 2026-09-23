<?php

declare(strict_types=1);

namespace Rokezzz\CategoryAdvance\Model\Pdf;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

final class CatalogPdfViewDataBuilder
{
    private const PLATFORM_SIGNATURE = 'System powered by Rokezzz';

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly Visibility $productVisibility,
        private readonly StoreManagerInterface $storeManager,
        private readonly PriceCurrencyInterface $priceCurrency
    ) {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws LocalizedException
     */
    public function build(int $categoryId): array
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $category = $this->loadCategory($categoryId, $storeId);
        $items = [];

        foreach ($this->getProductCollection($category) as $product) {
            /** @var Product $product */
            $items[] = [
                'sku' => (string) $product->getSku(),
                'name' => (string) $product->getName(),
                'price' => $this->formatPrice((float) $product->getFinalPrice(), $storeId),
            ];
        }

        return [
            'category_name' => (string) $category->getName(),
            'items' => $items,
            'empty_message' => (string) __('No products in this category.'),
            'platform_signature' => (string) __(self::PLATFORM_SIGNATURE),
        ];
    }

    /**
     * @throws LocalizedException
     */
    private function loadCategory(int $categoryId, int $storeId): CategoryInterface
    {
        if ($categoryId <= 0) {
            throw new LocalizedException(__('The category could not be found.'));
        }

        try {
            return $this->categoryRepository->get($categoryId, $storeId);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(__('The category could not be found.'));
        }
    }

    /**
     * Visible catalog products only — configurable children (variants) stay out via visibility.
     *
     * @return iterable<Product>
     */
    private function getProductCollection(CategoryInterface $category): iterable
    {
        if (!$category instanceof Category) {
            return [];
        }

        /** @var ProductCollection $collection */
        $collection = $category->getProductCollection();
        $collection->addAttributeToSelect(['name', 'sku', 'price']);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->addStoreFilter();
        $collection->addMinimalPrice();
        $collection->addFinalPrice();
        $collection->addTaxPercents();
        $collection->setOrder('name', 'ASC');

        return $collection;
    }

    private function formatPrice(float $amount, int $storeId): string
    {
        return $this->priceCurrency->format(
            $amount,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $storeId
        );
    }
}
