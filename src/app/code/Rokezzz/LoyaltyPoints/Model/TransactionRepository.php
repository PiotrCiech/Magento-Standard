<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterfaceFactory;
use Rokezzz\LoyaltyPoints\Api\TransactionRepositoryInterface;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction as TransactionResource;
use Rokezzz\LoyaltyPoints\Model\ResourceModel\Transaction\CollectionFactory;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly TransactionFactory $transactionFactory,
        private readonly TransactionResource $transactionResource,
        private readonly CollectionFactory $collectionFactory,
        private readonly TransactionSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    public function save(TransactionInterface $transaction): TransactionInterface
    {
        try {
            $this->transactionResource->save($transaction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save loyalty points transaction.'), $exception);
        }

        return $transaction;
    }

    public function getById(int $transactionId): TransactionInterface
    {
        $transaction = $this->transactionFactory->create();
        $this->transactionResource->load($transaction, $transactionId);

        if (!$transaction->getId()) {
            throw new NoSuchEntityException(__('Loyalty points transaction with id "%1" does not exist.', $transactionId));
        }

        return $transaction;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): TransactionSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
