<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;

class Author extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(AuthorResource::class);
    }
}
