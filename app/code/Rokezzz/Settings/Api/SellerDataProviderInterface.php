<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Api;

use Rokezzz\Settings\Api\Data\SellerDataInterface;

interface SellerDataProviderInterface
{
    public function get(?int $storeId = null): SellerDataInterface;
}
