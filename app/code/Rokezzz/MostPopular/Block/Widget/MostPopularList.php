<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Block\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersReportCollection;
use Magento\Widget\Block\BlockInterface;
use Rokezzz\MostPopular\Block\Product\MostPopular;
use Rokezzz\MostPopular\Model\Config;

class MostPopularList extends MostPopular implements BlockInterface
{
    protected $_template = 'Rokezzz_MostPopular::widget/most-popular.phtml';

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        UrlHelper $urlHelper,
        BestsellersReportCollection $bestsellersReportCollection,
        Visibility $productVisibility,
        Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $bestsellersReportCollection,
            $productVisibility,
            $config,
            $data
        );
    }

    public function getProductsCount(): int
    {
        $widgetCount = (int) $this->getData('products_count');

        if ($widgetCount > 0) {
            return $widgetCount;
        }

        return parent::getProductsCount();
    }

    public function getTitle(): string
    {
        return (string) $this->getData('title');
    }

    public function shouldRender(): bool
    {
        return $this->isEnabled() && $this->getLoadedProductCollection()->count() > 0;
    }

    public function getMode(): string
    {
        return 'grid';
    }

    public function getProductItemsHtml(): string
    {
        $originalTemplate = $this->getTemplate();
        $this->setTemplate('Rokezzz_MostPopular::widget/items.phtml');
        $html = (string) parent::_toHtml();
        $this->setTemplate($originalTemplate);

        return $html;
    }

    protected function _toHtml()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        return parent::_toHtml();
    }
}
