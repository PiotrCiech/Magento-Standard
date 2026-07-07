<?php

declare(strict_types=1);

namespace Rokezzz\LoyaltyPoints\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface TransactionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return TransactionInterface[]
     */
    public function getItems();

    /**
     * @param TransactionInterface[] $items
     */
    public function setItems(array $items);
}
