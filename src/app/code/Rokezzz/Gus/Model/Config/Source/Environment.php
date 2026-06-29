<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Environment implements OptionSourceInterface
{
    public const ENV_PROD = 'prod';
    public const ENV_DEV = 'dev';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ENV_PROD, 'label' => __('Production')],
            ['value' => self::ENV_DEV, 'label' => __('Test (sandbox)')],
        ];
    }
}
