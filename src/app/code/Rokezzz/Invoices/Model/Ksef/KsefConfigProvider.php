<?php

declare(strict_types=1);

namespace Rokezzz\Invoices\Model\Ksef;

use Rokezzz\Invoices\Exception\KsefApiException;
use Rokezzz\Settings\Model\Config as SettingsConfig;

class KsefConfigProvider
{
    public function __construct(
        private readonly SettingsConfig $settingsConfig
    ) {
    }

    public function getApiUrl(?int $storeId = null): string
    {
        $apiUrl = $this->settingsConfig->getKsefApiUrl($storeId);
        if ($apiUrl === '') {
            throw new KsefApiException('Missing KSeF API URL in Rokezzz Settings.');
        }

        return $apiUrl;
    }

    public function getToken(?int $storeId = null): string
    {
        $token = $this->settingsConfig->getKsefToken($storeId);
        if ($token === '') {
            throw new KsefApiException('Missing KSeF token in Rokezzz Settings.');
        }

        return $token;
    }

    public function getNip(?int $storeId = null): string
    {
        $nip = $this->settingsConfig->getKsefNip($storeId);
        if ($nip === '') {
            throw new KsefApiException('Missing KSeF NIP in Rokezzz Settings.');
        }

        return $nip;
    }
}
