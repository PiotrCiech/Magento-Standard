<?php

declare(strict_types=1);

namespace Rokezzz\PromotionSlider\Api\Data;

interface SlideInterface
{
    const TABLE_NAME = 'rokezzz_promotion_slide';
    const SLIDE_ID = 'slide_id';
    const TITLE = 'title';
    const IMAGE = 'image';
    const URL = 'url';
    const SORT_ORDER = 'sort_order';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function getSlideId(): ?int;
    public function setSlideId(int $slideId): self;
    public function getTitle(): ?string;
    public function setTitle(string $title): self;
    public function getImage(): ?string;
    public function setImage(?string $image): self;
    public function getUrl(): ?string;
    public function setUrl(?string $url): self;
    public function getSortOrder(): int;
    public function setSortOrder(int $sortOrder): self;
    public function getIsActive(): bool;
    public function setIsActive(bool $isActive): self;
}
