<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'post_id';

    protected function _construct(): void
    {
        $this->_init(
            \Rokezzz\Blog\Model\Post::class,
            \Rokezzz\Blog\Model\ResourceModel\Post::class
        );
    }
}
