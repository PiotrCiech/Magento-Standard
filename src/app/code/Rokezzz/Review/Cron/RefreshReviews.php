<?php

declare(strict_types=1);

namespace Rokezzz\Review\Cron;

use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Rokezzz\Review\Api\ReviewProviderInterface;
use Rokezzz\Review\Model\Config\ReviewConfig;
use Throwable;

class RefreshReviews
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ReviewConfig $reviewConfig,
        private readonly ReviewProviderInterface $reviewProvider,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int) $store->getId();
            if (!$this->reviewConfig->isEnabled($storeId) || !$this->reviewConfig->isConfigured($storeId)) {
                continue;
            }

            try {
                $this->reviewProvider->refresh($storeId);
            } catch (Throwable $throwable) {
                $this->logger->warning(
                    sprintf(
                        'Google reviews refresh failed for store %d: %s',
                        $storeId,
                        $throwable->getMessage()
                    )
                );
            }
        }
    }
}
