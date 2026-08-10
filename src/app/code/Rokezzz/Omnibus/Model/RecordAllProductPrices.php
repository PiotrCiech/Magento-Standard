<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Omnibus\Api\PriceHistoryRecorderInterface;

final class RecordAllProductPrices
{
    private const BATCH_SIZE = 500;

    public function __construct(
        private readonly Config $config,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly PriceHistoryRecorderInterface $priceHistoryRecorder,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function execute(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int) $store->getId();
            if (!$this->config->isEnabled($storeId)) {
                continue;
            }

            $this->recordForStore($storeId);
        }
    }

    private function recordForStore(int $storeId): void
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect(['price', 'special_price', 'tax_class_id']);
        $collection->addFieldToFilter('type_id', Type::TYPE_SIMPLE);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->setPageSize(self::BATCH_SIZE);

        $lastPage = (int) $collection->getLastPageNumber();
        for ($page = 1; $page <= $lastPage; $page++) {
            $collection->setCurPage($page);
            foreach ($collection as $product) {
                $this->priceHistoryRecorder->recordForProduct((int) $product->getId(), $storeId);
            }

            $collection->clear();
        }
    }
}
