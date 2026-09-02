<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Post\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

class Tag implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private readonly TagCollectionFactory $tagCollectionFactory
    ) {}

    public function toOptionArray(): array
    {
        if (!is_null($this->options)) {
            return $this->options;
        }

        $this->options = [];
        $collection = $this->tagCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $tag) {
            $this->options[] = [
                'value' => (int) $tag->getId(),
                'label' => (string) $tag->getName()
            ];
        }

        return $this->options;
    }
}
