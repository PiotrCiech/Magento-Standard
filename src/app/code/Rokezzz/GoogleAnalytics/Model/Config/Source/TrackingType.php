<?php

declare(strict_types=1);

namespace Rokezzz\GoogleAnalytics\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TrackingType implements OptionSourceInterface
{
    public const TYPE_NONE = '';
    public const TYPE_GA4 = 'ga4';
    public const TYPE_GTAG = 'gtag';
    public const TYPE_GTM = 'gtm';

    /**
     * @return array<int, array{value: string, label: \Magento\Framework\Phrase}>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::TYPE_NONE, 'label' => __('Disabled')],
            ['value' => self::TYPE_GA4, 'label' => __('GA4 (Google tag / gtag.js)')],
            ['value' => self::TYPE_GTAG, 'label' => __('Google tag (gtag.js)')],
            ['value' => self::TYPE_GTM, 'label' => __('Google Tag Manager')],
        ];
    }
}
