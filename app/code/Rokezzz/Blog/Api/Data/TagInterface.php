<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Api\Data;

interface TagInterface
{
    public const TABLE_NAME = 'rokezzz_blog_tag';
    public const TAG_ID = 'tag_id';
    public const NAME = 'name';
    public const IDENTIFIER = 'identifier';
    public const IS_ACTIVE = 'is_active';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getTagId(): ?int;

    public function setTagId(int $tagId): TagInterface;

    public function getName(): string;

    public function setName(string $name): TagInterface;

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier): TagInterface;
}
