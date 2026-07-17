<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterface;

class TransactionSearchResultsFactory
{
    public function create(): TransactionSearchResultsInterface
    {
        return new TransactionSearchResults();
    }
}
