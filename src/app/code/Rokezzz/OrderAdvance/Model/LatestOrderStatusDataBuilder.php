<?php

declare(strict_types=1);

namespace Rokezzz\OrderAdvance\Model;

use DateTimeImmutable;
use DateTimeZone;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

final class LatestOrderStatusDataBuilder
{
    public function __construct(
        private readonly Config $config,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(?Order $order): array
    {
        if (is_null($order) || !$this->isWithinVisibleDays($order)) {
            return ['visible' => false];
        }

        return [
            'visible' => true,
            'increment_id' => (string) $order->getIncrementId(),
            'status' => (string) $order->getStatus(),
            'status_label' => (string) $order->getFrontendStatusLabel(),
            'order_url' => $this->urlBuilder->getUrl(
                'sales/order/view',
                ['order_id' => (int) $order->getEntityId()]
            ),
        ];
    }

    private function isWithinVisibleDays(Order $order): bool
    {
        $createdAt = (string) $order->getCreatedAt();

        if ($createdAt === '') {
            return false;
        }

        $createdDate = new DateTimeImmutable($createdAt, new DateTimeZone('UTC'));
        $visibleDays = $this->config->getStatusBarVisibleDays((int) $order->getStoreId());
        $threshold = new DateTimeImmutable(
            sprintf('-%d days', $visibleDays),
            new DateTimeZone('UTC')
        );

        return $createdDate >= $threshold;
    }
}
