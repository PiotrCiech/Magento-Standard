<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemInterface;

interface OfferRequestProviderInterface
{
    public function getById(int $requestId): ?OfferRequestInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getByIdForCustomer(int $requestId, int $customerId): OfferRequestInterface;

    /**
     * @return OfferRequestItemInterface[]
     */
    public function getItemsByRequestId(int $requestId): array;

    /**
     * @return OfferRequestInterface[]
     */
    public function getListByCustomerId(int $customerId): array;
}
