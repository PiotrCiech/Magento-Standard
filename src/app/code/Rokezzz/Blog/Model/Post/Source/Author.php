<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Post\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Blog\Model\ResourceModel\Author\CollectionFactory as AuthorCollectionFactory;

class Author implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private readonly AuthorCollectionFactory $authorCollectionFactory
    ) {}

    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            ['value' => '', 'label' => __('-- Please Select --')]
        ];

        $collection = $this->authorCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $author) {
            $name = trim($author->getFirstname() . ' ' . $author->getLastname());
            $this->options[] = [
                'value' => (int) $author->getId(),
                'label' => $name ?: __('Author #%1', $author->getId())
            ];
        }

        return $this->options;
    }
}