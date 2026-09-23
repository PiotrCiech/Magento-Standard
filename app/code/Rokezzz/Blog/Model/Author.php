<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Api\Data\AuthorInterface;
use Rokezzz\Blog\Model\ResourceModel\Author as AuthorResource;

class Author extends AbstractModel implements AuthorInterface
{
    protected function _construct(): void
    {
        $this->_init(AuthorResource::class);
    }

    public function getAuthorId(): ?int
    {
        $authorId = $this->getData(self::AUTHOR_ID);

        return is_null($authorId) ? null : (int) $authorId;
    }

    public function setAuthorId(int $authorId): AuthorInterface
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    public function getFirstname(): string
    {
        return (string) $this->getData(self::FIRSTNAME);
    }

    public function setFirstname(string $firstname): AuthorInterface
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    public function getLastname(): string
    {
        return (string) $this->getData(self::LASTNAME);
    }

    public function setLastname(string $lastname): AuthorInterface
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    public function getJobTitle(): ?string
    {
        $jobTitle = $this->getData(self::JOB_TITLE);

        return is_null($jobTitle) ? null : (string) $jobTitle;
    }

    public function setJobTitle(?string $jobTitle): AuthorInterface
    {
        return $this->setData(self::JOB_TITLE, $jobTitle);
    }

    public function getAvatar(): ?string
    {
        $avatar = $this->getData(self::AVATAR);

        return is_null($avatar) ? null : (string) $avatar;
    }

    public function setAvatar(?string $avatar): AuthorInterface
    {
        return $this->setData(self::AVATAR, $avatar);
    }

    public function getIdentifier(): string
    {
        return (string) $this->getData(self::IDENTIFIER);
    }

    public function setIdentifier(string $identifier): AuthorInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }
}
