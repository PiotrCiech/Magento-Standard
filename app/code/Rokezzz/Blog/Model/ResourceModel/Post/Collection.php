<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rokezzz\Blog\Model\Post;
use Rokezzz\Blog\Model\ResourceModel\PostResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'post_id';

    protected function _construct(): void
    {
        $this->_init(Post::class, PostResource::class);
    }
}
