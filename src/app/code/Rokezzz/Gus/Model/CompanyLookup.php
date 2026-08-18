<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use Magento\Framework\Exception\LocalizedException;
use Rokezzz\Gus\Api\CompanyLookupInterface;
use Rokezzz\Gus\Api\Data\CompanyDataInterface;

class CompanyLookup implements CompanyLookupInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly NipNormalizer $nipNormalizer,
        private readonly RegonApiClient $regonApiClient,
        private readonly SearchReportMapper $searchReportMapper
    ) {
    }

    public function findByNip(string $nip, ?int $storeId = null): CompanyDataInterface
    {
        if (!$this->config->isEnabled($storeId)) {
            throw new LocalizedException(__('GUS lookup is disabled.'));
        }

        $apiKey = $this->config->getApiKey($storeId);

        if ($apiKey === '') {
            throw new LocalizedException(__('GUS API key is not configured.'));
        }

        $normalizedNip = $this->nipNormalizer->normalize($nip);
        $report = $this->regonApiClient->fetchByNip(
            $apiKey,
            $this->config->getEnvironment($storeId),
            $normalizedNip
        );

        return $this->searchReportMapper->map($report, $normalizedNip);
    }
}
