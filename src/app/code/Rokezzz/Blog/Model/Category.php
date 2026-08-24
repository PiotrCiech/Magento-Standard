<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Api\Data\CategoryInterface;
use Rokezzz\Blog\Model\ResourceModel\CategoryResource;

class Category extends AbstractModel implements CategoryInterface
{
    protected function _construct(): void
    {
        $this->_init(CategoryResource::class);
    }

    public function getCategoryId(): ?int
    {
        $categoryId = $this->getData(self::CATEGORY_ID);

        return is_null($categoryId) ? null : (int) $categoryId;
    }

    public function setCategoryId(int $categoryId): CategoryInterface
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): CategoryInterface
    {
        return $this->setData(self::NAME, $name);
    }
}
