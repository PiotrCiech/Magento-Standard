<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Tag;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Blog\Model\ResourceModel\Tag\CollectionCreator;

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

        foreach ($this->collection->getItems() as $tag) {
            $this->loadedData[(int) $tag->getId()] = $tag->getData();
        }

        $data = $this->dataPersistor->get('rokezzz_blog_tag');
        if (is_array($data) && $data !== []) {
            $tag = $this->collection->getNewEmptyItem();
            $tag->setData($data);
            $this->loadedData[(int) $tag->getId()] = $tag->getData();
            $this->dataPersistor->clear('rokezzz_blog_tag');
        }

        return $this->loadedData;
    }
}
