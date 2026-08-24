<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Rokezzz\Blog\Api\Data\CommentInterface;

class CommentResource extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(CommentInterface::TABLE_NAME, CommentInterface::COMMENT_ID);
    }
}
