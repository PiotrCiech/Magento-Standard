<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\LocalizedException;

interface QuoteRequestCartManagementInterface
{
    /**
     * @throws LocalizedException
     */
    public function addToCart(int $customerId, int $productId, float $qty): void;
}
