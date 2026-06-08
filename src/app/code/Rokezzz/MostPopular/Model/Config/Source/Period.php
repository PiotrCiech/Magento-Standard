<?php

declare(strict_types=1);

namespace Rokezzz\MostPopular\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Period implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'day', 'label' => __('Daily')],
            ['value' => 'month', 'label' => __('Monthly')],
            ['value' => 'year', 'label' => __('Yearly')],
        ];
    }
}
