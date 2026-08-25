<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Model\ResourceModel\Comment as CommentResource;

class Comment extends AbstractModel
{
    public const STATUS_APPROVED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_NOT_APPROVED = 3;

    protected function _construct(): void
    {
        $this->_init(CommentResource::class);
    }
}
