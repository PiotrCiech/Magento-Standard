<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tag extends AbstractDb
{
    public const TABLE_NAME = 'rokezzz_blog_tag';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'tag_id');
    }
}
