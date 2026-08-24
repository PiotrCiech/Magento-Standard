<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\ObjectManagerInterface;

final class AuthorCreator
{
    public function __construct(private readonly ObjectManagerInterface $objectManager)
    {
    }

    public function create(): Author
    {
        return $this->objectManager->create(Author::class);
    }
}
