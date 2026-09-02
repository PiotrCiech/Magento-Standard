<?php
declare(strict_types=1);

namespace Rokezzz\Blog\ViewModel;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use DateTime;
use Rokezzz\Blog\Model\AuthorFactory;
use Rokezzz\Blog\Model\CategoryFactory;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;
use Rokezzz\Blog\Model\ResourceModel\Category as CategoryResource;
use Rokezzz\Blog\Model\ResourceModel\Post\Collection;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionCreator;
use Rokezzz\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

class PostList implements ArgumentInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly CollectionCreator $postCollectionCreator,
        private readonly StoreManagerInterface $storeManager,
        private readonly AuthorFactory $authorFactory,
        private readonly AuthorResource $authorResource,
        private readonly CategoryFactory $categoryFactory,
        private readonly CategoryResource $categoryResource,
        private readonly TagCollectionFactory $tagCollectionFactory,
    ) {
    }

    public function getPosts(): Collection
    {
        $collection = $this->postCollectionCreator->create();
        $storeId = (int)$this->storeManager->getStore()->getId();

        $collection->addFieldToFilter('main_table.is_active', 1)
            ->addFieldToFilter('main_table.published_at', ['lteq' => (new DateTime())->format('Y-m-d H:i:s')])
            ->setOrder('main_table.published_at', 'DESC');

        // Filtrowanie po Store View
        $collection->getSelect()
            ->join(
                ['store_table' => $collection->getTable('rokezzz_blog_post_store')],
                'main_table.post_id = store_table.post_id',
                []
            )
            ->where('store_table.store_id IN (?)', [0, $storeId])
            ->group('main_table.post_id');

        // Filtr kategorii
        if ($this->request->getControllerName() === 'category') {
            $catIdentifier = (string)$this->request->getParam('identifier');
            $collection->getSelect()
                ->join(['cat' => $collection->getTable('rokezzz_blog_category')], 'main_table.category_id = cat.category_id', [])
                ->where('cat.identifier = ?', $catIdentifier);
        }

        // Filtr autora
        if ($this->request->getControllerName() === 'author') {
            $authIdentifier = (string)$this->request->getParam('identifier');
            $collection->getSelect()
                ->join(['auth' => $collection->getTable('rokezzz_blog_author')], 'main_table.author_id = auth.author_id', [])
                ->where('auth.identifier = ?', $authIdentifier);
        }

        // Filtr tagu
        if ($this->request->getControllerName() === 'tag') {
            $tagIdentifier = (string)$this->request->getParam('identifier');
            $collection->getSelect()
                ->join(['pt' => $collection->getTable('rokezzz_blog_post_tag')], 'main_table.post_id = pt.post_id', [])
                ->join(['t' => $collection->getTable('rokezzz_blog_tag')], 'pt.tag_id = t.tag_id', [])
                ->where('t.identifier = ?', $tagIdentifier);
        }

        return $collection;
    }

    public function getAuthor(int $authorId): ?object
    {
        $author = $this->authorFactory->create();
        $this->authorResource->load($author, $authorId);
        return $author->getId() ? $author : null;
    }

    public function getCategory(int $categoryId): ?object
    {
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $categoryId);
        return $category->getId() ? $category : null;
    }

    public function getTagsForPost(int $postId): array
    {
        $tagCollection = $this->tagCollectionFactory->create();
        $tagCollection->getSelect()
            ->join(['pt' => $tagCollection->getTable('rokezzz_blog_post_tag')], 'main_table.tag_id = pt.tag_id', [])
            ->where('pt.post_id = ?', $postId)
            ->where('main_table.is_active = 1');

        return $tagCollection->getItems();
    }

public function getImageUrl(?string $image): string
{
    if (!$image) {
        return '';
    }
    if (str_starts_with($image, 'http')) {
        return $image;
    }
    $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    $cleanPath = ltrim($image, '/');

    if (
        str_starts_with($cleanPath, 'catalog/') ||
        str_starts_with($cleanPath, 'wysiwyg/') ||
        str_starts_with($cleanPath, 'blog/')
    ) {
        return $mediaUrl . $cleanPath;
    }

    return $mediaUrl . 'blog/tmp/image/' . $cleanPath;
}
}
