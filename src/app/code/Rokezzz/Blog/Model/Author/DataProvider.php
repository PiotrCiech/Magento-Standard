<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Author;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Blog\Model\ResourceModel\Author\CollectionCreator;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionCreator $collectionCreator,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly StoreManagerInterface $storeManager,
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

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $mediaUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        foreach ($this->collection->getItems() as $author) {
            $data = $author->getData();

            if (!empty($data['avatar'])) {
                $imagePath = (string) $data['avatar'];

                if (str_starts_with($imagePath, 'http')) {
                    $imageUrl = $imagePath;
                } elseif (str_starts_with($imagePath, 'blog/') || str_starts_with($imagePath, 'wysiwyg/')) {
                    $imageUrl = $mediaUrl . ltrim($imagePath, '/');
                } else {
                    $imageUrl = $mediaUrl . 'blog/tmp/image/' . ltrim($imagePath, '/');
                }

                $data['avatar'] = [
                    [
                        'name' => basename($imagePath),
                        'url' => $imageUrl,
                        'size' => 0,
                        'type' => 'image',
                    ],
                ];
            }

            $this->loadedData[(int) $author->getId()] = $data;
        }

        $data = $this->dataPersistor->get('rokezzz_blog_author');
        if (is_array($data) && $data !== []) {
            $author = $this->collection->getNewEmptyItem();
            $author->setData($data);
            $this->loadedData[(int) $author->getId()] = $author->getData();
            $this->dataPersistor->clear('rokezzz_blog_author');
        }

        return $this->loadedData;
    }
}
