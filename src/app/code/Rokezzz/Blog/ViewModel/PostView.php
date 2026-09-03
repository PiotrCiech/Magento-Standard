<?php

declare(strict_types=1);

namespace Rokezzz\Blog\ViewModel;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Blog\Api\Data\CommentInterface;
use Rokezzz\Blog\Model\AuthorFactory;
use Rokezzz\Blog\Model\CategoryFactory;
use Rokezzz\Blog\Model\Post;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;
use Rokezzz\Blog\Model\ResourceModel\Comment\CollectionCreator as CommentCollectionCreator;
use Rokezzz\Blog\Model\ResourceModel\Post\CollectionCreator;
use Rokezzz\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

class PostView implements ArgumentInterface
{
    private ?Post $currentPost = null;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly CollectionCreator $postCollectionCreator,
        private readonly StoreManagerInterface $storeManager,
        private readonly AuthorFactory $authorFactory,
        private readonly AuthorResource $authorResource,
        private readonly CategoryFactory $categoryFactory,
        private readonly CategoryResource $categoryResource,
        private readonly TagCollectionFactory $tagCollectionFactory,
        private readonly CommentCollectionCreator $commentCollectionCreator,
        private readonly FilterProvider $filterProvider,
        private readonly FormKey $formKey
    ) {
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    public function getPost(): ?Post
    {
        if (!is_null($this->currentPost)) {
            return $this->currentPost;
        }

        $identifier = (string) $this->request->getParam('identifier');
        $collection = $this->postCollectionCreator->create();
        $collection->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('is_active', 1);

        $this->currentPost = $collection->getFirstItem();

        return $this->currentPost->getId() ? $this->currentPost : null;
    }

    public function renderContent(string $content): string
    {
        return $this->filterProvider->getPageFilter()->filter($content);
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

    public function getComments(int $postId): array
    {
        $comments = $this->commentCollectionCreator->create();
        $comments->addFieldToFilter(CommentInterface::POST_ID, $postId)
            ->addFieldToFilter(CommentInterface::STATUS, CommentInterface::STATUS_APPROVED)
            ->setOrder(CommentInterface::CREATED_AT, 'ASC');

        return $comments->getItems();
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
