<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Tag;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\ResourceModel\Tag as TagResource;
use Rokezzz\Blog\Model\Tag as TagModel;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(TagModel::class, TagResource::class);
    }
}