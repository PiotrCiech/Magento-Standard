<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Block\Widget;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Rokezzz\Blog\Model\Config;
use Rokezzz\Blog\Model\ResourceModel\Post\Collection;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class LatestPosts extends Template implements BlockInterface
{
    protected $_template = 'Rokezzz_Blog::widget/latest-posts.phtml';

    private ?Collection $posts = null;

    public function __construct(
        Template\Context $context,
        private readonly PostCollectionFactory $postCollectionFactory,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getTitle(): string
    {
        return (string) $this->getData('title');
    }

    public function getPostsCount(): int
    {
        $widgetCount = (int) $this->getData('posts_count');

        return $widgetCount > 0 ? $widgetCount : $this->config->getLatestPostsCount();
    }

    public function getPosts(): Collection
    {
        if ($this->posts !== null) {
            return $this->posts;
        }

        $storeId = (int) $this->_storeManager->getStore()->getId();

        $collection = $this->postCollectionFactory->create();
        $collection->addFieldToFilter('main_table.is_active', 1)
            ->addFieldToFilter('main_table.published_at', ['lteq' => (new \DateTime())->format('Y-m-d H:i:s')])
            ->setOrder('main_table.published_at', 'DESC')
            ->setPageSize($this->getPostsCount());

        $collection->getSelect()
            ->join(
                ['store_table' => $collection->getTable('rokezzz_blog_post_store')],
                'main_table.post_id = store_table.post_id',
                []
            )
            ->where('store_table.store_id IN (?)', [0, $storeId])
            ->group('main_table.post_id');

        $this->posts = $collection;

        return $this->posts;
    }

    public function getImageUrl(?string $image): string
    {
        if (!$image) {
            return '';
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }

        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $cleanPath = ltrim($image, '/');

        if (str_starts_with($cleanPath, 'blog/')) {
            return $mediaUrl . $cleanPath;
        }

        return $mediaUrl . 'blog/tmp/image/' . $cleanPath;
    }

    public function shouldRender(): bool
    {
        return $this->config->isLatestPostsEnabled() && $this->getPosts()->count() > 0;
    }

    protected function _toHtml()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        return parent::_toHtml();
    }
}
