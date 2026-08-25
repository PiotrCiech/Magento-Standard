<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Comment\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Blog\Model\Comment;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => Comment::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => Comment::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Comment::STATUS_NOT_APPROVED, 'label' => __('Not Approved')],
        ];
    }
}
