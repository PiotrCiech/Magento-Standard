<?php

declare(strict_types=1);

namespace Rokezzz\Review\Api\Data;

interface GoogleReviewsInterface
{
    public function isEnabled(): bool;

    public function getPlaceId(): string;

    public function getAverageRating(): float;

    public function getReviewCount(): int;

    public function getLastUpdatedAt(): ?string;

    /**
     * @return GoogleReviewInterface[]
     */
    public function getItems(): array;
}
