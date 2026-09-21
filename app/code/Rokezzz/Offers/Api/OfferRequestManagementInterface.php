<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;

interface OfferRequestManagementInterface
{
    /**
     * @param OfferRequestItemDataInterface[] $items
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function submit(int $customerId, string $customerNote, array $items): OfferRequestInterface;
}
