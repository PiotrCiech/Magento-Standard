<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Model\ResourceModel\PostResource;

class Post extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(PostResource::class);
    }
}
