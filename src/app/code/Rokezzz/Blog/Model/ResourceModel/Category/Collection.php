<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\Category as CategoryModel;
use Rokezzz\Blog\Model\ResourceModel\Category as CategoryResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(CategoryModel::class, CategoryResource::class);
    }
}