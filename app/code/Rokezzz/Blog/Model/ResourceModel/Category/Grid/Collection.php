<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Category\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected $_idFieldName = 'category_id';
}
