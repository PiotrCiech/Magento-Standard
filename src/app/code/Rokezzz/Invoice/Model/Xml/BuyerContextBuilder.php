<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Xml;

use Magento\Directory\Model\CountryFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

final class BuyerContextBuilder
{
    public function __construct(
        private readonly CountryFactory $countryFactory
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(InvoiceInterface $invoice): array
    {
        $address = $invoice->getOrder()?->getBillingAddress();
        if (!$address instanceof OrderAddressInterface) {
            return $this->emptyContext();
        }

        $countryCode = strtoupper((string) $address->getCountryId());
        $buyerNip = '';
        $buyerVat = '';
        $vatNumber = trim((string) $address->getVatId());

        if ($countryCode === 'PL') {
            $buyerNip = preg_replace('/\D+/', '', $vatNumber) ?? '';
        } else {
            $buyerVat = $vatNumber;
        }

        return [
            'buyerName' => $this->resolveBuyerName($address),
            'buyerNip' => $buyerNip,
            'buyerVat' => $buyerVat,
            'buyerStreet' => $this->resolveStreet($address),
            'buyerPost' => trim($address->getPostcode() . ' ' . $address->getCity()),
            'buyerCountry' => $this->resolveCountryName($countryCode),
            'buyerCountryCode' => $countryCode !== '' ? $countryCode : 'PL',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function emptyContext(): array
    {
        return [
            'buyerName' => '',
            'buyerNip' => '',
            'buyerVat' => '',
            'buyerStreet' => '',
            'buyerPost' => '',
            'buyerCountry' => 'Polska',
            'buyerCountryCode' => 'PL',
        ];
    }

    private function resolveBuyerName(OrderAddressInterface $address): string
    {
        $company = trim((string) $address->getCompany());
        if ($company !== '') {
            return $company;
        }

        return trim($address->getFirstname() . ' ' . $address->getLastname());
    }

    private function resolveStreet(OrderAddressInterface $address): string
    {
        $street = $address->getStreet();

        if (!is_array($street)) {
            return trim((string) $street);
        }

        return trim(implode(' ', array_filter($street)));
    }

    private function resolveCountryName(string $countryCode): string
    {
        if ($countryCode === '') {
            return 'Polska';
        }

        $country = $this->countryFactory->create()->loadByCode($countryCode);
        $name = trim((string) $country->getName());

        return $name !== '' ? $name : $countryCode;
    }
}
