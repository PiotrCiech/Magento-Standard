<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\ObjectManagerInterface;

final class CommentCreator
{
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    public function create(): Comment
    {
        return $this->objectManager->create(Comment::class);
    }
}
