<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\ObjectManagerInterface;

final class TagCreator
{
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    public function create(): Tag
    {
        return $this->objectManager->create(Tag::class);
    }
}
