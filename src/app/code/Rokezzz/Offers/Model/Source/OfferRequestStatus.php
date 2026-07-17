<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;

class OfferRequestStatus implements OptionSourceInterface
{
    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => OfferRequestInterface::STATUS_NEW, 'label' => __('New')],
            ['value' => OfferRequestInterface::STATUS_IN_REVIEW, 'label' => __('In Review')],
            ['value' => OfferRequestInterface::STATUS_OFFERED, 'label' => __('Offered')],
            ['value' => OfferRequestInterface::STATUS_ACCEPTED, 'label' => __('Accepted')],
            ['value' => OfferRequestInterface::STATUS_REJECTED, 'label' => __('Rejected')],
            ['value' => OfferRequestInterface::STATUS_EXPIRED, 'label' => __('Expired')],
        ];
    }
}
