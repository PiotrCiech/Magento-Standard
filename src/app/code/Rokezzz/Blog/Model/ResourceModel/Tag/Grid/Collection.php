<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Tag\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected $_idFieldName = 'tag_id';
}
