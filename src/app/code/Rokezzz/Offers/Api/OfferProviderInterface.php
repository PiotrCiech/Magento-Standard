<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemInterface;

interface OfferProviderInterface
{
    public function getById(int $offerId): ?OfferInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getByIdForCustomer(int $offerId, int $customerId): OfferInterface;

    /**
     * @return OfferInterface[]
     */
    public function getListByCustomerId(int $customerId): array;

    /**
     * @return OfferItemInterface[]
     */
    public function getItemsByOfferId(int $offerId): array;
}
