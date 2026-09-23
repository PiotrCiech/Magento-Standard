<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Rokezzz\Blog\Api\Data\TagInterface;

class Tag extends AbstractDb
{
    public const TABLE_NAME = TagInterface::TABLE_NAME;

    protected function _construct(): void
    {
        $this->_init(TagInterface::TABLE_NAME, TagInterface::TAG_ID);
    }
}
