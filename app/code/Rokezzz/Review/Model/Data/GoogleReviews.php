<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model\Data;

use Rokezzz\Review\Api\Data\GoogleReviewInterface;
use Rokezzz\Review\Api\Data\GoogleReviewsInterface;

class GoogleReviews implements GoogleReviewsInterface
{
    /**
     * @param GoogleReviewInterface[] $items
     */
    public function __construct(
        private readonly bool $enabled,
        private readonly string $placeId,
        private readonly float $averageRating,
        private readonly int $reviewCount,
        private readonly ?string $lastUpdatedAt,
        private readonly array $items
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }

    public function getLastUpdatedAt(): ?string
    {
        return $this->lastUpdatedAt;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
