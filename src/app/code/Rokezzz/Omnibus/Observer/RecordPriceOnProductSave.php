<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Rokezzz\Omnibus\Api\PriceHistoryRecorderInterface;

class RecordPriceOnProductSave implements ObserverInterface
{
    public function __construct(
        private readonly PriceHistoryRecorderInterface $priceHistoryRecorder
    ) {
    }

    public function execute(Observer $observer): void
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product instanceof Product) {
            return;
        }

        $this->priceHistoryRecorder->recordForAllStores($product);
    }
}
