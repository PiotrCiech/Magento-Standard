<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\LocalizedException;

interface OfferApplicatorInterface
{
    /**
     * @throws LocalizedException
     */
    public function applyToCart(int $offerId, int $customerId): void;
}
