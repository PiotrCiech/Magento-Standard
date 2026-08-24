<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\Category;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'category_id';

    protected function _construct(): void
    {
        $this->_init(Category::class, CategoryResource::class);
    }
}
