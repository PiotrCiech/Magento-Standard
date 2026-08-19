<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Api\Data;

interface CategoryInterface
{
    public const TABLE_NAME = 'rokezzz_blog_category';
    public const CATEGORY_ID = 'category_id';
    public const NAME = 'name';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getCategoryId(): ?int;

    public function setCategoryId(int $categoryId): CategoryInterface;

    public function getName(): string;

    public function setName(string $name): CategoryInterface;
}
