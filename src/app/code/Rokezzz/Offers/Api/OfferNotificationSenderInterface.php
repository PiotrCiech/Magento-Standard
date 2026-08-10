<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Api;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Offers\Api\Data\OfferInterface;

interface OfferNotificationSenderInterface
{
    /**
     * @throws LocalizedException
     */
    public function sendOfferSentNotification(OfferInterface $offer): void;
}
