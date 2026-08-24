<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Category;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Blog\Model\ResourceModel\Category\CollectionCreator;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionCreator $collectionCreator,
        private readonly DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionCreator->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!is_null($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];

        foreach ($this->collection->getItems() as $category) {
            $this->loadedData[(int) $category->getId()] = $category->getData();
        }

        $data = $this->dataPersistor->get('rokezzz_blog_category');
        if (is_array($data) && $data !== []) {
            $category = $this->collection->getNewEmptyItem();
            $category->setData($data);
            $this->loadedData[(int) $category->getId()] = $category->getData();
            $this->dataPersistor->clear('rokezzz_blog_category');
        }

        return $this->loadedData;
    }
}
