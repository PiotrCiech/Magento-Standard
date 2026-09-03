<?php

declare(strict_types=1);

namespace Rokezzz\ShippingPayment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const XML_PATH_ENABLED = 'rokezzz_shipping_payment/general/enabled';
    private const XML_PATH_MAPPING = 'rokezzz_shipping_payment/payment_methods/mapping';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Json $json
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns allowed payment method codes for a shipping carrier, or null when unrestricted.
     *
     * @return string[]|null
     */
    public function getAllowedPaymentMethodCodes(string $carrierCode, ?int $storeId = null): ?array
    {
        if ($carrierCode === '') {
            return null;
        }

        return $this->getMapping($storeId)[$carrierCode] ?? null;
    }

    /**
     * @return array<string, string[]>
     */
    public function getMapping(?int $storeId = null): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_MAPPING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (is_string($value) && $value !== '') {
            $value = $this->json->unserialize($value);
        }

        return is_array($value) ? $this->normalizeMapping($value) : [];
    }

    /**
     * @param array<mixed> $mapping
     * @return array<string, string[]>
     */
    private function normalizeMapping(array $mapping): array
    {
        $normalized = [];

        foreach ($mapping as $carrierCode => $payments) {
            if (!is_array($payments)) {
                continue;
            }

            $codes = array_values(array_filter(array_map('strval', $payments)));

            if ($codes === []) {
                continue;
            }

            $normalized[(string) $carrierCode] = $codes;
        }

        return $normalized;
    }
}
