<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\ObjectManagerInterface;

final class PostCreator
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(): Post
    {
        return $this->objectManager->create(Post::class);
    }
}
