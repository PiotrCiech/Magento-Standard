<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Post;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Rokezzz\Blog\Model\ResourceModel\Post as PostResource;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly StoreManagerInterface $storeManager,
        private readonly PostResource $postResource,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();
        $mediaUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $items = $this->collection->getItems();

        foreach ($items as $model) {
            $data = $model->getData();
            $postId = (int) $model->getId();

            $data['tag_ids'] = $this->postResource->getTagIds($postId);
            $data['store_ids'] = $this->postResource->getStoreIds($postId);

            if (!empty($data['background_image'])) {
                $imagePath = (string) $data['background_image'];

                if (str_starts_with($imagePath, 'http')) {
                    $imageUrl = $imagePath;
                } elseif (str_starts_with($imagePath, 'blog/') || str_starts_with($imagePath, 'wysiwyg/')) {
                    $imageUrl = $mediaUrl . ltrim($imagePath, '/');
                } else {
                    $imageUrl = $mediaUrl . 'blog/tmp/image/' . ltrim($imagePath, '/');
                }

                $data['background_image'] = [
                    [
                        'name' => basename($imagePath),
                        'url'  => $imageUrl,
                        'size' => 0,
                        'type' => 'image'
                    ]
                ];
            }

            $this->loadedData[$postId] = $data;
        }

        $data = $this->dataPersistor->get('rokezzz_blog_post');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('rokezzz_blog_post');
        }

        return $this->loadedData;
    }
}