<?php

declare(strict_types=1);

namespace Rokezzz\Review\Api\Data;

interface GoogleReviewInterface
{
    public function getAuthorName(): string;

    public function getRating(): int;

    public function getText(): string;

    public function getRelativeTimeDescription(): string;

    public function getReviewTimestamp(): int;
}
