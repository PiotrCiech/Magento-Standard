<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\ResourceModel\Offer\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected $_idFieldName = 'entity_id';
}
