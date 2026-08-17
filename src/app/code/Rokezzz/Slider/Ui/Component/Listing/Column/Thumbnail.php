<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    private StoreManagerInterface $storeManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

            foreach ($dataSource['data']['items'] as &$item) {
                if (!empty($item['image'])) {
                    $item[$fieldName . '_src'] = $baseUrl . 'rokezzz/promotionslider/' . ltrim($item['image'], '/');
                    $item[$fieldName . '_alt'] = $item['title'] ?? '';
                    $item[$fieldName . '_orig_src'] = $baseUrl . 'rokezzz/promotionslider/' . ltrim($item['image'], '/');
                }
            }
        }

        return $dataSource;
    }
}
