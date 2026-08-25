<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Comment\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected $_idFieldName = 'comment_id';

    protected function _initSelect(): self
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['post' => $this->getTable('rokezzz_blog_post')],
            'main_table.post_id = post.post_id',
            ['post_title' => 'post.title']
        );

        return $this;
    }
}
