<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Comment\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Rokezzz\Blog\Api\Data\CommentInterface;

class Collection extends SearchResult
{
    protected $_idFieldName = 'comment_id';

    protected function _initSelect(): self
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['post' => $this->getTable('rokezzz_blog_post')],
            'main_table.' . CommentInterface::POST_ID . ' = post.post_id',
            ['post_title' => 'title']
        );
        $this->addFilterToMap('post_title', 'post.title');
        $this->addFilterToMap('comment_id', 'main_table.comment_id');
        $this->addFilterToMap('status', 'main_table.status');
        $this->addFilterToMap('created_at', 'main_table.created_at');

        return $this;
    }
}
