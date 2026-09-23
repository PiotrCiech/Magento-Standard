<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\Comment\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Blog\Api\Data\CommentInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => CommentInterface::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => CommentInterface::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => CommentInterface::STATUS_REJECTED, 'label' => __('Rejected')],
        ];
    }
}
