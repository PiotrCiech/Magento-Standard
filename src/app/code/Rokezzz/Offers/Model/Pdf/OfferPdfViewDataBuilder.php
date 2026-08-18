<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Pdf;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Rokezzz\Offers\Api\Data\OfferInterface;
use Rokezzz\Offers\Api\Data\OfferItemInterface;
use Rokezzz\Offers\Api\OfferProviderInterface;

class OfferPdfViewDataBuilder
{
    private const PLATFORM_SIGNATURE = 'System powered by Rokezzz';

    public function __construct(
        private readonly OfferProviderInterface $offerProvider,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly TimezoneInterface $timezone
    ) {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws LocalizedException
     */
    public function build(OfferInterface $offer): array
    {
        $offerId = $offer->getEntityId();

        if (is_null($offerId)) {
            throw new LocalizedException(__('The offer could not be prepared for PDF export.'));
        }

        $storeId = $this->resolveStoreId($offer);
        $items = $this->offerProvider->getItemsByOfferId((int) $offerId);
        $lineItems = [];
        $total = 0.0;

        foreach ($items as $item) {
            $lineTotal = $item->getOfferedQty() * $item->getOfferedPrice();
            $total += $lineTotal;
            $lineItems[] = $this->buildLineItem($item, $lineTotal, $storeId);
        }

        return [
            'increment_id' => $offer->getIncrementId(),
            'expires_at' => $this->formatDate($offer->getExpiresAt()),
            'customer_name' => $this->resolveCustomerName($offer),
            'customer_note' => $offer->getCustomerNote(),
            'admin_note' => $offer->getAdminNote(),
            'items' => $lineItems,
            'total' => $this->formatPrice($total, $storeId),
            'platform_signature' => (string) __(self::PLATFORM_SIGNATURE),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function buildLineItem(OfferItemInterface $item, float $lineTotal, int $storeId): array
    {
        return [
            'sku' => $item->getSku(),
            'name' => $item->getName(),
            'qty' => $this->formatQty($item->getOfferedQty()),
            'price' => $this->formatPrice($item->getOfferedPrice(), $storeId),
            'line_total' => $this->formatPrice($lineTotal, $storeId),
        ];
    }

    private function resolveStoreId(OfferInterface $offer): int
    {
        try {
            $customer = $this->customerRepository->getById($offer->getCustomerId());

            return (int) $customer->getStoreId();
        } catch (NoSuchEntityException) {
            return (int) $this->storeManager->getStore()->getId();
        }
    }

    private function resolveCustomerName(OfferInterface $offer): string
    {
        try {
            $customer = $this->customerRepository->getById($offer->getCustomerId());

            return trim($customer->getFirstname() . ' ' . $customer->getLastname());
        } catch (NoSuchEntityException) {
            return '';
        }
    }

    private function formatPrice(float $amount, int $storeId): string
    {
        return $this->priceCurrency->format($amount, false, PriceCurrencyInterface::DEFAULT_PRECISION, $storeId);
    }

    private function formatQty(float $qty): string
    {
        return rtrim(rtrim(number_format($qty, 4, '.', ''), '0'), '.');
    }

    private function formatDate(?string $date): string
    {
        if (is_null($date) || trim($date) === '') {
            return '';
        }

        return (string) $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, true);
    }
}
