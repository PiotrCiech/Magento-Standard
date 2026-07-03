<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionInterface;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterface;

interface TransactionRepositoryInterface
{
    public function save(TransactionInterface $transaction): TransactionInterface;

    public function getById(int $transactionId): TransactionInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): TransactionSearchResultsInterface;
}
