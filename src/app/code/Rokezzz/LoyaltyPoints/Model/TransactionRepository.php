<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterface;
use Rokezzz\LoyaltyPoints\Api\TransactionRepositoryInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction as TransactionResource;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction\CollectionProvider;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly TransactionResource $transactionResource,
        private readonly CollectionProvider $transactionCollectionProvider,
        private readonly TransactionSearchResultsFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function createNew(): TransactionInterface
    {
        $transaction = $this->transactionCollectionProvider->create()->getNewEmptyItem();

        if (!$transaction instanceof Transaction) {
            throw new CouldNotSaveException(__('Invalid loyalty points transaction entity.'));
        }

        return $transaction;
    }

    public function save(TransactionInterface $transaction): TransactionInterface
    {
        if (!$transaction instanceof Transaction) {
            throw new CouldNotSaveException(__('Invalid loyalty points transaction entity.'));
        }

        try {
            $this->transactionResource->save($transaction);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save loyalty points transaction.'), $exception);
        }

        return $transaction;
    }

    public function getById(int $transactionId): TransactionInterface
    {
        $collection = $this->transactionCollectionProvider->create();
        $collection->addFieldToFilter(TransactionInterface::TRANSACTION_ID, $transactionId);
        $transaction = $collection->getFirstItem();

        if (!$transaction instanceof Transaction || !$transaction->getId()) {
            throw new NoSuchEntityException(__('Loyalty points transaction with id "%1" does not exist.', $transactionId));
        }

        return $transaction;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): TransactionSearchResultsInterface
    {
        $collection = $this->transactionCollectionProvider->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
