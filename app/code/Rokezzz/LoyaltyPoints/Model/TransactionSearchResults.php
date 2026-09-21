<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Model;

use Magento\Framework\Api\SearchResults;
use Rokezzz\LoyaltyPoints\Api\Data\TransactionSearchResultsInterface;

class TransactionSearchResults extends SearchResults implements TransactionSearchResultsInterface
{
}
