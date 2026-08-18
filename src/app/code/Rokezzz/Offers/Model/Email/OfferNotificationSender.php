<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Email;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url;
use Psr\Log\LoggerInterface;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\OfferNotificationSenderInterface;
use Rokezzz\Offers\Api\OfferPdfGeneratorInterface;
use Rokezzz\Offers\Model\Mail\Template\TransportBuilder;

class OfferNotificationSender implements OfferNotificationSenderInterface
{
    private const EMAIL_TEMPLATE = 'rokezzz_offers_offer_sent';

    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OfferPdfGeneratorInterface $offerPdfGenerator,
        private readonly TransportBuilder $transportBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly TimezoneInterface $timezone,
        private readonly Url $frontendUrlBuilder,
        private readonly LoggerInterface $logger
    ) {
    }

    public function sendOfferSentNotification(OfferInterface $offer): void
    {
        $customer = $this->getCustomerOrThrow($offer);
        $storeId = $this->resolveStoreId($customer);
        $pdfContent = $this->offerPdfGenerator->generate($offer);
        $attachmentName = sprintf('offer-%s.pdf', $offer->getIncrementId());

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::EMAIL_TEMPLATE)
                ->setTemplateOptions([
                    'area' => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars($this->buildTemplateVars($offer, $customer, $storeId))
                ->setFromByScope('general', $storeId)
                ->addTo($customer->getEmail(), $this->resolveCustomerName($customer))
                ->addAttachment($pdfContent, $attachmentName, 'application/pdf')
                ->getTransport();

            $transport->sendMessage();
        } catch (MailException $mailException) {
            $this->logger->error('Offer notification email could not be sent.', [
                'offer_id' => $offer->getEntityId(),
                'exception' => $mailException,
            ]);

            throw new LocalizedException(
                __('The offer was saved, but the customer email could not be sent. Please try again or contact the customer manually.')
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTemplateVars(
        OfferInterface $offer,
        CustomerInterface $customer,
        int $storeId
    ): array {
        return [
            'customer_name' => $this->resolveCustomerName($customer),
            'offer_number' => $offer->getIncrementId(),
            'offer_view_url' => $this->buildOfferViewUrl($offer, $storeId),
            'expires_at' => $this->formatDate($offer->getExpiresAt()),
            'store_name' => $this->scopeConfig->getValue(
                'general/store_information/name',
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?: $this->storeManager->getStore($storeId)->getName(),
        ];
    }

    private function buildOfferViewUrl(OfferInterface $offer, int $storeId): string
    {
        return $this->frontendUrlBuilder
            ->setScope($storeId)
            ->getUrl(
                'rokezzz_offers/offer/view',
                [
                    '_scope' => $storeId,
                    '_nosid' => true,
                    'entity_id' => $offer->getEntityId(),
                ]
            );
    }

    private function formatDate(?string $date): string
    {
        if (is_null($date) || trim($date) === '') {
            return '';
        }

        return (string) $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, true);
    }

    private function resolveCustomerName(CustomerInterface $customer): string
    {
        return trim($customer->getFirstname() . ' ' . $customer->getLastname());
    }

    private function resolveStoreId(CustomerInterface $customer): int
    {
        $storeId = (int) $customer->getStoreId();

        if ($storeId > 0) {
            return $storeId;
        }

        return (int) $this->storeManager->getStore()->getId();
    }

    /**
     * @throws LocalizedException
     */
    private function getCustomerOrThrow(OfferInterface $offer): CustomerInterface
    {
        try {
            return $this->customerRepository->getById($offer->getCustomerId());
        } catch (NoSuchEntityException $noSuchEntityException) {
            throw new LocalizedException(
                __('The customer for this offer could not be found.'),
                $noSuchEntityException
            );
        }
    }
}
