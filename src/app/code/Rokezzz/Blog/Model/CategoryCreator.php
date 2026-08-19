<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\ObjectManagerInterface;

class CategoryCreator
{
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    public function create(): Category
    {
        return $this->objectManager->create(Category::class);
    }
}
