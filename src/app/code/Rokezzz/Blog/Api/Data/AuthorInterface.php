<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Api\Data;

interface AuthorInterface
{
    public const TABLE_NAME = 'rokezzz_blog_author';
    public const AUTHOR_ID = 'author_id';
    public const FIRSTNAME = 'firstname';
    public const LASTNAME = 'lastname';
    public const JOB_TITLE = 'job_title';
    public const AVATAR = 'avatar';
    public const IDENTIFIER = 'identifier';
    public const IS_ACTIVE = 'is_active';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getAuthorId(): ?int;

    public function setAuthorId(int $authorId): AuthorInterface;

    public function getFirstname(): string;

    public function setFirstname(string $firstname): AuthorInterface;

    public function getLastname(): string;

    public function setLastname(string $lastname): AuthorInterface;

    public function getJobTitle(): ?string;

    public function setJobTitle(?string $jobTitle): AuthorInterface;

    public function getAvatar(): ?string;

    public function setAvatar(?string $avatar): AuthorInterface;

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier): AuthorInterface;
}
