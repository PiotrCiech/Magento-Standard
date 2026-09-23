<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Model;

use Magento\Framework\Model\AbstractModel;
use Rokezzz\Slider\Api\Data\SlideInterface;
use Rokezzz\Slider\Model\ResourceModel\Slide as SlideResource;

class Slide extends AbstractModel implements SlideInterface
{
    protected $_eventPrefix = 'rokezzz_promotion_slide';

    protected function _construct(): void
    {
        $this->_init(SlideResource::class);
    }

    public function getSlideId(): ?int
    {
        $id = $this->getData(self::SLIDE_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setSlideId(int $slideId): SlideInterface
    {
        return $this->setData(self::SLIDE_ID, $slideId);
    }

    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle(string $title): SlideInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage(?string $image): SlideInterface
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function getUrl(): ?string
    {
        return $this->getData(self::URL);
    }

    public function setUrl(?string $url): SlideInterface
    {
        return $this->setData(self::URL, $url);
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData(self::SORT_ORDER);
    }

    public function setSortOrder(int $sortOrder): SlideInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): SlideInterface
    {
        return $this->setData(self::IS_ACTIVE, (int)$isActive);
    }
}
