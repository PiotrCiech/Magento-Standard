<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Blog\Api\Data\TagInterface;
use Rokezzz\Blog\Model\ResourceModel\Tag as TagResource;

class Tag extends AbstractModel implements TagInterface
{
    protected function _construct(): void
    {
        $this->_init(TagResource::class);
    }

    public function getTagId(): ?int
    {
        $tagId = $this->getData(self::TAG_ID);

        return is_null($tagId) ? null : (int) $tagId;
    }

    public function setTagId(int $tagId): TagInterface
    {
        return $this->setData(self::TAG_ID, $tagId);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): TagInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getIdentifier(): string
    {
        return (string) $this->getData(self::IDENTIFIER);
    }

    public function setIdentifier(string $identifier): TagInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }
}
