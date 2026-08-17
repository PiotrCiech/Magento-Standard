<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Model\Slide;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Slider\Model\ResourceModel\Slide\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;
    private StoreManagerInterface $storeManager;
    private ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->collection->getItems();

        foreach ($items as $slide) {
            $data = $slide->getData();

            if (!empty($data['image'])) {
                $imageName = $data['image'];
                $data['image'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->getMediaUrl($imageName),
                    ],
                ];
            }

            $this->loadedData[$slide->getId()] = $data;
        }

        $data = $this->dataPersistor->get('rokezzz_slider_slide');
        if (!empty($data)) {
            $slide = $this->collection->getNewEmptyItem();
            $slide->setData($data);
            $this->loadedData[$slide->getId()] = $slide->getData();
            $this->dataPersistor->clear('rokezzz_slider_slide');
        }

        return $this->loadedData;
    }

    private function getMediaUrl(string $imagePath): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $baseUrl . 'rokezzz/promotionslider/' . ltrim($imagePath, '/');
    }
}
