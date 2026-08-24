<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Category extends AbstractDb
{
    public const TABLE_NAME = 'rokezzz_blog_category';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'category_id');
    }
}
