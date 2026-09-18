<?php

declare(strict_types=1);

namespace Rokezzz\Tax\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Tax\Model\Config as TaxConfig;

final class PriceDisplayMode implements OptionSourceInterface
{
    /**
     * @return array<int, array{value: int, label: \Magento\Framework\Phrase}>
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                'label' => __('Including Tax'),
            ],
            [
                'value' => TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX,
                'label' => __('Excluding Tax'),
            ],
        ];
    }
}
