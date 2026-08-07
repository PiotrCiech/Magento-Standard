<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model;

use Throwable;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;
use Rokezzz\Offers\Api\Data\OfferRequestItemDataInterface;
use Rokezzz\Offers\Api\OfferRequestManagementInterface;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestItemResource;
use Rokezzz\Offers\Model\ResourceModel\OfferRequestResource;

class OfferRequestManagement implements OfferRequestManagementInterface
{
    public function __construct(
        private readonly OfferRequestCreator $offerRequestCreator,
        private readonly OfferRequestItemCreator $offerRequestItemCreator,
        private readonly OfferRequestResource $offerRequestResource,
        private readonly OfferRequestItemResource $offerRequestItemResource,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @param OfferRequestItemDataInterface[] $items
     */
    public function submit(int $customerId, string $customerNote, array $items): OfferRequestInterface
    {
        $this->assertCustomerIsLoggedIn($customerId);
        $this->assertItemsAreValid($items);

        $offerRequest = $this->offerRequestCreator->create();
        $offerRequest->setCustomerId($customerId);
        $offerRequest->setStatus(OfferRequestInterface::STATUS_NEW);
        $offerRequest->setCustomerNote($customerNote);

        try {
            $this->offerRequestResource->save($offerRequest);

            foreach ($items as $itemData) {
                $this->saveRequestItem($offerRequest, $itemData);
            }
        } catch (LocalizedException $localizedException) {
            throw $localizedException;
        } catch (Throwable $throwable) {
            throw new CouldNotSaveException(
                __('The offer request could not be saved right now. Please try again later.'),
                $throwable
            );
        }

        return $offerRequest;
    }

    /**
     * @throws LocalizedException
     */
    private function assertCustomerIsLoggedIn(int $customerId): void
    {
        if ($customerId <= 0) {
            throw new LocalizedException(__('You must be logged in to submit an offer request.'));
        }
    }

    /**
     * @param OfferRequestItemDataInterface[] $items
     *
     * @throws LocalizedException
     */
    private function assertItemsAreValid(array $items): void
    {
        if ($items === []) {
            throw new LocalizedException(__('Please add at least one product to the offer request.'));
        }

        foreach ($items as $itemData) {
            if (!$itemData instanceof OfferRequestItemDataInterface) {
                throw new LocalizedException(__('The offer request items are invalid.'));
            }

            if ($itemData->getProductId() <= 0) {
                throw new LocalizedException(__('Each offer request item must include a valid product.'));
            }

            if ($itemData->getQty() <= 0) {
                throw new LocalizedException(__('Each offer request item must include a quantity greater than zero.'));
            }
        }
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    private function saveRequestItem(OfferRequestInterface $offerRequest, OfferRequestItemDataInterface $itemData): void
    {
        $product = $this->productRepository->getById($itemData->getProductId());

        $offerRequestItem = $this->offerRequestItemCreator->create();
        $offerRequestItem->setRequestId((int) $offerRequest->getEntityId());
        $offerRequestItem->setProductId($itemData->getProductId());
        $offerRequestItem->setSku((string) $product->getSku());
        $offerRequestItem->setName((string) $product->getName());
        $offerRequestItem->setQty($itemData->getQty());
        $offerRequestItem->setCustomerNote($itemData->getCustomerNote());

        try {
            $this->offerRequestItemResource->save($offerRequestItem);
        } catch (Throwable $throwable) {
            throw new CouldNotSaveException(
                __('The offer request could not be saved right now. Please try again later.'),
                $throwable
            );
        }
    }
}
