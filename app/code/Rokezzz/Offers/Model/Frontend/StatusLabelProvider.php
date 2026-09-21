<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Frontend;

use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferRequestInterface;

class StatusLabelProvider
{
    public function getOfferStatusLabel(string $status): string
    {
        return match ($status) {
            OfferInterface::STATUS_NEW => (string) __('New'),
            OfferInterface::STATUS_IN_REVIEW => (string) __('In Review'),
            OfferInterface::STATUS_OFFERED => (string) __('Offered'),
            OfferInterface::STATUS_ACCEPTED => (string) __('Accepted'),
            OfferInterface::STATUS_REJECTED => (string) __('Rejected'),
            OfferInterface::STATUS_EXPIRED => (string) __('Expired'),
            default => $status,
        };
    }

    public function getOfferStatusClass(string $status): string
    {
        return match ($status) {
            OfferInterface::STATUS_OFFERED => 'rokezzz-status--offered',
            OfferInterface::STATUS_ACCEPTED => 'rokezzz-status--accepted',
            OfferInterface::STATUS_REJECTED => 'rokezzz-status--rejected',
            OfferInterface::STATUS_EXPIRED => 'rokezzz-status--expired',
            OfferInterface::STATUS_IN_REVIEW => 'rokezzz-status--in-review',
            default => 'rokezzz-status--default',
        };
    }

    public function getRequestStatusLabel(string $status): string
    {
        return match ($status) {
            OfferRequestInterface::STATUS_NEW => (string) __('New'),
            OfferRequestInterface::STATUS_IN_REVIEW => (string) __('In Review'),
            OfferRequestInterface::STATUS_OFFERED => (string) __('Offered'),
            OfferRequestInterface::STATUS_ACCEPTED => (string) __('Accepted'),
            OfferRequestInterface::STATUS_REJECTED => (string) __('Rejected'),
            OfferRequestInterface::STATUS_EXPIRED => (string) __('Expired'),
            default => $status,
        };
    }

    public function getRequestStatusClass(string $status): string
    {
        return $this->getOfferStatusClass($status);
    }
}
