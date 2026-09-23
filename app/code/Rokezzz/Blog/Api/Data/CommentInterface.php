<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Api\Data;

interface CommentInterface
{
    public const TABLE_NAME = 'rokezzz_blog_comment';
    public const COMMENT_ID = 'comment_id';
    public const POST_ID = 'post_id';
    public const AUTHOR_NAME = 'author_name';
    public const AUTHOR_EMAIL = 'author_email';
    public const CONTENT = 'content';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function getCommentId(): ?int;

    public function setCommentId(int $commentId): CommentInterface;

    public function getPostId(): int;

    public function setPostId(int $postId): CommentInterface;

    public function getAuthorName(): string;

    public function setAuthorName(string $authorName): CommentInterface;

    public function getAuthorEmail(): ?string;

    public function setAuthorEmail(?string $authorEmail): CommentInterface;

    public function getContent(): string;

    public function setContent(string $content): CommentInterface;

    public function getStatus(): string;

    public function setStatus(string $status): CommentInterface;
}
