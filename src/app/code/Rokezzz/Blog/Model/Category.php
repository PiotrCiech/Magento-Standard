<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Model\ResourceModel\Category as CategoryResource;

class Category extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(CategoryResource::class);
    }
}
