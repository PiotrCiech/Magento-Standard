<?php

declare(strict_types=1);

namespace Rokezzz\Settings\Model;

use Rokezzz\Settings\Api\CompanySignatureProviderInterface;
use Rokezzz\Settings\Api\SellerDataProviderInterface;

class CompanySignatureProvider implements CompanySignatureProviderInterface
{
    public function __construct(
        private readonly SellerDataProviderInterface $sellerDataProvider
    ) {
    }

    public function getLines(?int $storeId = null): array
    {
        $seller = $this->sellerDataProvider->get($storeId);
        $lines = [];

        if ($seller->getCompanyName() !== '') {
            $lines[] = $seller->getCompanyName();
        }

        $taxLine = $this->buildTaxLine($seller->getNip(), $seller->getRegon());
        if ($taxLine !== '') {
            $lines[] = $taxLine;
        }

        $contactLine = $this->buildContactLine($seller->getContactEmail(), $seller->getContactPhone());
        if ($contactLine !== '') {
            $lines[] = $contactLine;
        }

        if ($seller->getWebsiteUrl() !== '') {
            $lines[] = $seller->getWebsiteUrl();
        }

        return $lines;
    }

    public function getHtml(?int $storeId = null): string
    {
        $lines = $this->getLines($storeId);

        if ($lines === []) {
            return '';
        }

        return implode('<br>', array_map('htmlspecialchars', $lines));
    }

    public function getPlainText(?int $storeId = null): string
    {
        return implode(PHP_EOL, $this->getLines($storeId));
    }

    private function buildTaxLine(string $nip, string $regon): string
    {
        $parts = [];

        if ($nip !== '') {
            $parts[] = 'NIP: ' . $nip;
        }

        if ($regon !== '') {
            $parts[] = 'REGON: ' . $regon;
        }

        return implode(' | ', $parts);
    }

    private function buildContactLine(string $email, string $phone): string
    {
        $parts = array_values(array_filter([$email, $phone]));

        return implode(' | ', $parts);
    }
}
