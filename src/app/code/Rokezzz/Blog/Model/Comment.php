<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Api\Data\CommentInterface;
use Rokezzz\Blog\Model\ResourceModel\CommentResource;

final class Comment extends AbstractModel implements CommentInterface
{
    protected function _construct(): void
    {
        $this->_init(CommentResource::class);
    }

    public function getCommentId(): ?int
    {
        $commentId = $this->getData(self::COMMENT_ID);

        return is_null($commentId) ? null : (int) $commentId;
    }

    public function setCommentId(int $commentId): CommentInterface
    {
        return $this->setData(self::COMMENT_ID, $commentId);
    }

    public function getPostId(): int
    {
        return (int) $this->getData(self::POST_ID);
    }

    public function setPostId(int $postId): CommentInterface
    {
        return $this->setData(self::POST_ID, $postId);
    }

    public function getAuthorName(): string
    {
        return (string) $this->getData(self::AUTHOR_NAME);
    }

    public function setAuthorName(string $authorName): CommentInterface
    {
        return $this->setData(self::AUTHOR_NAME, $authorName);
    }

    public function getAuthorEmail(): ?string
    {
        $authorEmail = $this->getData(self::AUTHOR_EMAIL);

        return is_null($authorEmail) ? null : (string) $authorEmail;
    }

    public function setAuthorEmail(?string $authorEmail): CommentInterface
    {
        return $this->setData(self::AUTHOR_EMAIL, $authorEmail);
    }

    public function getContent(): string
    {
        return (string) $this->getData(self::CONTENT);
    }

    public function setContent(string $content): CommentInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getStatus(): string
    {
        return (string) $this->getData(self::STATUS);
    }

    public function setStatus(string $status): CommentInterface
    {
        return $this->setData(self::STATUS, $status);
    }
}
