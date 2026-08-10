<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;

interface QuoteRequestSubmissionManagementInterface
{
    /**
     * @throws LocalizedException
     */
    public function submitFromCart(int $customerId, string $customerNote): OfferRequestInterface;
}
