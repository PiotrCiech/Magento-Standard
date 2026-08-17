<?php

declare(strict_types=1);

namespace Rokezzz\Slider\Model\ResourceModel\Slide;

use Magento\Framework\ObjectManagerInterface;

class CollectionCreator
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
