<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Author;

use Magento\Framework\Filter\FilterManager;
use Rokezzz\Blog\Api\Data\AuthorInterface;
use Rokezzz\Blog\Model\Author;

final class PostDataProcessor
{
    public function __construct(private readonly FilterManager $filterManager)
    {
    }

    public function prepare(array $data, Author $author): array
    {
        unset($data['form_key'], $data[AuthorInterface::AUTHOR_ID]);

        $data[AuthorInterface::AVATAR] = $this->resolveAvatar($data[AuthorInterface::AVATAR] ?? null);
        $data[AuthorInterface::IDENTIFIER] = $this->resolveIdentifier($data, $author);

        return $data;
    }

    private function resolveAvatar(mixed $avatar): ?string
    {
        $file = is_array($avatar) ? ($avatar[0] ?? []) : [];
        $name = (string) ($file['name'] ?? '');
        if ($name !== '') {
            return $name;
        }

        $url = (string) ($file['url'] ?? '');
        if ($url !== '') {
            return basename($url);
        }

        return null;
    }

    private function resolveIdentifier(array $data, Author $author): string
    {
        $identifier = (string) ($data[AuthorInterface::IDENTIFIER] ?? '');
        if ($identifier !== '') {
            return $identifier;
        }

        if ($author->getIdentifier() !== '') {
            return $author->getIdentifier();
        }

        $slug = (string) ($data[AuthorInterface::FIRSTNAME] ?? '')
            . '-'
            . (string) ($data[AuthorInterface::LASTNAME] ?? '');

        return $this->filterManager->translitUrl($slug);
    }
}
