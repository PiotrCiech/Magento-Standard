<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Cron;

use Psr\Log\LoggerInterface;
use Rokezzz\Offers\Model\OfferExpirationManager;

class ExpireOffers
{
    public function __construct(
        private readonly OfferExpirationManager $offerExpirationManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        $expiredCount = $this->offerExpirationManager->expireDueOffers();

        if ($expiredCount > 0) {
            $this->logger->info('Rokezzz Offers: expired due offers.', ['count' => $expiredCount]);
        }
    }
}
