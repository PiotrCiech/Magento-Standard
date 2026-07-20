<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Offers\Api\Data\OfferInterface;

class OfferStatus implements OptionSourceInterface
{
    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => OfferInterface::STATUS_IN_REVIEW, 'label' => __('In Review')],
            ['value' => OfferInterface::STATUS_OFFERED, 'label' => __('Offered')],
            ['value' => OfferInterface::STATUS_ACCEPTED, 'label' => __('Accepted')],
            ['value' => OfferInterface::STATUS_REJECTED, 'label' => __('Rejected')],
            ['value' => OfferInterface::STATUS_EXPIRED, 'label' => __('Expired')],
        ];
    }
}
