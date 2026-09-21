<?php

declare(strict_types=1);

namespace Rokezzz\Review\Model\Data;

use Rokezzz\Review\Api\Data\GoogleReviewInterface;

class GoogleReview implements GoogleReviewInterface
{
    public function __construct(
        private readonly string $authorName,
        private readonly int $rating,
        private readonly string $text,
        private readonly string $relativeTimeDescription,
        private readonly int $reviewTimestamp
    ) {
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getRelativeTimeDescription(): string
    {
        return $this->relativeTimeDescription;
    }

    public function getReviewTimestamp(): int
    {
        return $this->reviewTimestamp;
    }
}
