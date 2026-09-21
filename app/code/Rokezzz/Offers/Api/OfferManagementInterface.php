<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemDataInterface;

interface OfferManagementInterface
{
    /**
     * @param OfferItemDataInterface[] $items
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createFromRequest(
        int $requestId,
        string $adminNote,
        ?string $expiresAt,
        array $items
    ): OfferInterface;

    /**
     * @param OfferItemDataInterface[] $items
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function update(
        int $offerId,
        string $adminNote,
        ?string $expiresAt,
        array $items
    ): OfferInterface;

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function markAsOffered(int $offerId): OfferInterface;

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function reject(int $offerId): OfferInterface;
}
