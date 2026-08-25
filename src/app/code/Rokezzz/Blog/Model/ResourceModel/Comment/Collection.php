<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Comment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'comment_id';

    protected function _construct(): void
    {
        $this->_init(
            \Rokezzz\Blog\Model\Comment::class,
            \Rokezzz\Blog\Model\ResourceModel\Comment::class
        );
    }
}
