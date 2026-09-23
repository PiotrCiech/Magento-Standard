<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Comment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\Comment;
use Rokezzz\Blog\Model\ResourceModel\CommentResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'comment_id';

    protected function _construct(): void
    {
        $this->_init(Comment::class, CommentResource::class);
    }
}
