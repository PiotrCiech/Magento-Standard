<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Rokezzz\Blog\Api\Data\AuthorInterface;

class Author extends AbstractDb
{
    public const TABLE_NAME = AuthorInterface::TABLE_NAME;

    protected function _construct(): void
    {
        $this->_init(AuthorInterface::TABLE_NAME, AuthorInterface::AUTHOR_ID);
    }
}
