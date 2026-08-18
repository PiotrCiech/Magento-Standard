<?php

declare(strict_types=1);

namespace Rokezzz\OrderOwnerNotification\Model;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

final class OrderOwnerNotificationEmailSender
{
    public function __construct(
        private readonly Config $config,
        private readonly TransportBuilder $transportBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function send(OrderOwnerNotificationContext $context): void
    {
        try {
            $this->dispatch($context);
        } catch (MailException $exception) {
            $this->logger->error(
                'Order owner notification email could not be sent.',
                [
                    'order_id' => $context->order->getEntityId(),
                    'increment_id' => $context->order->getIncrementId(),
                    'exception' => $exception,
                ]
            );
        }
    }

    /**
     * @throws MailException
     */
    private function dispatch(OrderOwnerNotificationContext $context): void
    {
        $store = $this->storeManager->getStore($context->storeId);

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->config->getEmailTemplate($context->storeId))
            ->setTemplateOptions([
                'area' => Area::AREA_FRONTEND,
                'store' => $context->storeId,
            ])
            ->setTemplateVars($this->buildTemplateVars($context, $store))
            ->setFromByScope('general', $context->storeId)
            ->addTo($context->recipientEmail)
            ->getTransport();

        $transport->sendMessage();
    }

    private function buildTemplateVars(
        OrderOwnerNotificationContext $context,
        StoreInterface $store
    ): array {
        return [
            'order' => $context->order,
            'store' => $store,
            'formatted_grand_total' => $context->order->formatPrice(
                (float) $context->order->getGrandTotal()
            ),
        ];
    }
}
