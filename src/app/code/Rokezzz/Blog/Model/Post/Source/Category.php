<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Post\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class Category implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private readonly CategoryCollectionFactory $categoryCollectionFactory
    ) {}

    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            ['value' => '', 'label' => __('-- Please Select --')]
        ];

        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $category) {
            $this->options[] = [
                'value' => (int) $category->getId(),
                'label' => (string) $category->getName()
            ];
        }

        return $this->options;
    }
}
