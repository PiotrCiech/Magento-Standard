<?php

declare(strict_types=1);

namespace Rokezzz\Gus\Model;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\SearchReport;
use Magento\Framework\Exception\LocalizedException;

class RegonApiClient
{
    public function __construct(
        private readonly GusApiFactory $gusApiFactory,
        private readonly GusApiExceptionMapper $exceptionMapper
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function fetchByNip(string $apiKey, string $environment, string $nip): SearchReport
    {
        $gus = $this->gusApiFactory->create($apiKey, $environment);

        try {
            return $this->fetchReport($gus, $nip);
        } catch (InvalidUserKeyException|NotFoundException $exception) {
            throw $this->exceptionMapper->toLocalizedException($exception);
        } finally {
            $this->logout($gus);
        }
    }

    private function fetchReport(GusApi $gus, string $nip): SearchReport
    {
        $gus->login();
        $reports = $gus->getByNip($nip);

        if ($reports === []) {
            throw new LocalizedException(__('No company found for the given NIP.'));
        }

        return $reports[0];
    }

    private function logout(GusApi $gus): void
    {
        if (!$gus->isLogged()) {
            return;
        }

        $gus->logout();
    }
}
