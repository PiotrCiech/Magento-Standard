<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Rokezzz\Blog\Api\Data\CategoryInterface;

class CategoryResource extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(CategoryInterface::TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }
}
