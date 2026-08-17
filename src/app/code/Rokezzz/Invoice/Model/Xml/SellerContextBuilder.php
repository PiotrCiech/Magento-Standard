<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Xml;

use Rokezzz\Settings\Api\SellerDataProviderInterface;

final class SellerContextBuilder
{
    public function __construct(
        private readonly SellerDataProviderInterface $sellerDataProvider
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function build(?int $storeId = null): array
    {
        $seller = $this->sellerDataProvider->get($storeId);

        return [
            'sellerNip' => $seller->getNip(),
            'sellerName' => $seller->getCompanyName(),
            'sellerAddressLine1' => $seller->getStreetAddress(),
            'sellerAddressLine2' => $seller->getCityPostcode(),
            'sellerCountryCode' => $seller->getCountryCode(),
            'placeOfIssue' => $seller->getPlaceOfIssue(),
            'bankAccountNumber' => $seller->getBankAccountNumber(),
            'systemInfo' => $seller->getSystemInfo(),
        ];
    }
}
