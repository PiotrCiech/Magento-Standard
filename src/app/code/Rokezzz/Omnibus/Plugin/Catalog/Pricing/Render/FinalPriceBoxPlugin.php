<?php

declare(strict_types=1);

namespace Rokezzz\Omnibus\Plugin\Catalog\Pricing\Render;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Render\FinalPriceBox;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Omnibus\Model\Config;
use Rokezzz\Omnibus\ViewModel\OmnibusPrice;

final class FinalPriceBoxPlugin
{
    private const LISTING_ZONES = ['item_list', 'item_grid', 'category_list'];

    public function __construct(
        private readonly BlockFactory $blockFactory,
        private readonly Config $config,
        private readonly OmnibusPrice $omnibusPriceViewModel,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function afterToHtml(FinalPriceBox $subject, string $result): string
    {
        if (!$this->isListingZone($subject->getZone())) {
            return $result;
        }

        $storeId = (int) $this->storeManager->getStore()->getId();
        if (!$this->config->isEnabled($storeId) || !$this->config->isShowOnListing($storeId)) {
            return $result;
        }

        $saleableItem = $subject->getSaleableItem();
        if (!$saleableItem instanceof Product) {
            return $result;
        }

        if (!$this->omnibusPriceViewModel->shouldRender($saleableItem, true)) {
            return $result;
        }

        $block = $this->blockFactory->createBlock(Template::class)
            ->setTemplate('Rokezzz_Omnibus::omnibus-price.phtml')
            ->setData('product', $saleableItem)
            ->setData('listing_context', true)
            ->setData('view_model', $this->omnibusPriceViewModel);

        return $result . $block->toHtml();
    }

    private function isListingZone(string $zone): bool
    {
        return in_array($zone, self::LISTING_ZONES, true);
    }
}
