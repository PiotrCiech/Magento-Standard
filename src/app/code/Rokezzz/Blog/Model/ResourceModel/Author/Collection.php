<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Author;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\Author as AuthorModel;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(AuthorModel::class, AuthorResource::class);
    }
}