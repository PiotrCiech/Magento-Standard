<?php

declare(strict_types=1);

namespace Rokezzz\Review\Api;

use Rokezzz\Review\Api\Data\GoogleReviewsInterface;

interface ReviewProviderInterface
{
    public function get(?int $storeId = null): GoogleReviewsInterface;

    public function refresh(?int $storeId = null): GoogleReviewsInterface;
}
