<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel\Author;

use Magento\Framework\ObjectManagerInterface;

class CollectionCreator
{
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    public function create(): Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
